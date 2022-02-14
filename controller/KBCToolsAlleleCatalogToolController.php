<?php

namespace App\Http\Controllers\System\Tools;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\KBCClasses\DBAdminWrapperClass;
use App\KBCClasses\DBKBCWrapperClass;

class KBCToolsAlleleCatalogToolController extends Controller
{
    function __construct()
    {
        $this->db_kbc_wrapper = new DBKBCWrapperClass;
    }

    public function AlleleCatalogToolPage(Request $request, $organism)
    {
        $admin_db_wapper = new DBAdminWrapperClass;

        // Database
        $db = "KBC_" . $organism;

        // Table names and datasets
        if ($organism == "Zmays") {
            $table_name = "Zmays_Panzea_AGPv3_Allele_Catalog";
            $dataset_array = array("Zmays_Panzea_AGPv3_Allele_Catalog");

            // Query gene from database
            $sql = "SELECT DISTINCT Gene FROM " . $db . "." . $table_name . " WHERE Gene IS NOT NULL AND Gene LIKE 'GRMZM%' LIMIT 3;";
            $gene_array = DB::connection($db)->select($sql);
        } elseif ($organism == "Athaliana") {
            $table_name = "Athaliana_Arabidopsis_TAIR10_Allele_Catalog";
            $dataset_array = array("Athaliana_Arabidopsis_TAIR10_Allele_Catalog");

            // Query gene from database
            $sql = "SELECT DISTINCT Gene FROM " . $db . "." . $table_name . " WHERE Gene IS NOT NULL LIMIT 3;";
            $gene_array = DB::connection($db)->select($sql);
        }

        if (isset($table_name)) {
            // Query accession from database
            $sql = "SELECT DISTINCT Accession FROM " . $db . "." . $table_name . " WHERE Accession IS NOT NULL LIMIT 3;";
            $accession_array = DB::connection($db)->select($sql);

            // Package variables that need to go to the view
            $info = [
                'organism' => $organism,
                'dataset_array' => $dataset_array,
                'gene_array' => $gene_array,
                'accession_array' => $accession_array,
            ];

            // Return to view
            return view('system/tools/AlleleCatalogTool/AlleleCatalogTool')->with('info', $info);
        } else {
            // Package variables that need to go to the view
            $info = [
                'organism' => $organism,
            ];

            // Return to view
            return view('system/tools/AlleleCatalogTool/AlleleCatalogToolNotAvailable')->with('info', $info);
        }
    }


    // dataProcessor
    public function processAccessionCounts($accessionCounts)
    {
        $splittedAccessionCounts = array();

        for ($i = 0; $i < count($accessionCounts); $i++) {
            if (array_key_exists($accessionCounts[$i]->Gene, $splittedAccessionCounts)) {
                array_push($splittedAccessionCounts[$accessionCounts[$i]->Gene], $accessionCounts[$i]);
            } else {
                $splittedAccessionCounts[$accessionCounts[$i]->Gene] = array();
                array_push($splittedAccessionCounts[$accessionCounts[$i]->Gene], $accessionCounts[$i]);
            }
        }

        return $splittedAccessionCounts;
    }


    public function ViewAllByGenesPage(Request $request, $organism)
    {
        $admin_db_wapper = new DBAdminWrapperClass;

        // Database
        $db = "KBC_" . $organism;

        $dataset1 = $request->dataset1;
        $gene1 = $request->gene1;

        // Parse genes
        $gene_arr = preg_split("/[;, \n]+/", $gene1);
        for ($i = 0; $i < count($gene_arr); $i++) {
            $gene_arr[$i] = trim($gene_arr[$i]);
        }

        // Construct sql then make query
        if ($organism == "Zmays") {
            $sql = "
            SELECT COUNT(IF(Improvement_Status = 'Improved', 1, null)) AS Improved_Cultivar, 
            COUNT(IF(Improvement_Status = 'Landrace', 1, null)) AS Landrace, 
            COUNT(IF(Improvement_Status = 'Other', 1, null)) AS Other, 
            COUNT(IF(Improvement_Status IN ('Improved', 'Cultivar', 'Elite', 'Landrace', 'Genetic', 'Other') OR Improvement_Status IS NULL, 1, null)) AS Total, 
            COUNT(IF(Imputation = '+', 1, null)) AS Imputed, 
            COUNT(IF(Imputation = '-', 1, null)) AS Unimputed, 
            Gene, Position, Genotype, Genotype_with_Description 
            FROM " . $db . "." . $dataset1 . " 
            WHERE (Gene IN ('";
            for ($i = 0; $i < count($gene_arr); $i++) {
                if ($i < (count($gene_arr) - 1)) {
                    $sql = $sql . $gene_arr[$i] . "', '";
                } else {
                    $sql = $sql . $gene_arr[$i];
                }
            }
            $sql = $sql . "')) 
            GROUP BY Gene, Position, Genotype, Genotype_with_Description 
            ORDER BY Gene, Position, Total DESC;";
        } else {
            $sql = "
            SELECT COUNT(IF(Improvement_Status IN ('Improved', 'Cultivar', 'Elite', 'Landrace', 'Genetic', 'Other') OR Improvement_Status IS NULL, 1, null)) AS Total, 
            COUNT(IF(Imputation = '+', 1, null)) AS Imputed, 
            COUNT(IF(Imputation = '-', 1, null)) AS Unimputed, 
            Gene, Position, Genotype, Genotype_with_Description 
            FROM " . $db . "." . $dataset1 . " 
            WHERE (Gene IN ('";
            for ($i = 0; $i < count($gene_arr); $i++) {
                if ($i < (count($gene_arr) - 1)) {
                    $sql = $sql . $gene_arr[$i] . "', '";
                } else {
                    $sql = $sql . $gene_arr[$i];
                }
            }
            $sql = $sql . "')) 
            GROUP BY Gene, Position, Genotype, Genotype_with_Description 
            ORDER BY Gene, Position, Total DESC;";
        }
        

        $result_arr = DB::connection($db)->select($sql);
        $result_arr = $this->processAccessionCounts($result_arr);

        // Package variables that need to go to the view
        $info = [
            'organism' => $organism,
            'dataset1' => $dataset1,
            'gene1' => $gene1,
            'result_arr' => $result_arr,
        ];

        // Return to view
        return view('system/tools/AlleleCatalogTool/viewAllByGenes')->with('info', $info);
    }


    // getAccessionsByImpStatusGenePositionGenotypeDesc
    public function GetAccessionsByImpStatusGenePositionGenotypeDesc(Request $request, $organism)
    {
        $dataset = $request->Dataset;
        $key = $request->Key;
        $gene = $request->Gene;
        $position = $request->Position;
        $genotypeWithDescription = $request->GenotypeWithDescription;
        $organism = $request->Organism;

        // Database
        $db = "KBC_" . $organism;

        if (preg_match("/improved.cultivar/i", strval($key))) {
            $sql = "
                SELECT Classification, Improvement_Status, Maturity_Group, Country, State, 
                Accession, Gene, Position, Genotype, Genotype_with_Description, Imputation 
                FROM " . $db . "." . $dataset . "
                WHERE (Gene IN ('" . $gene . "'))
                AND Position = '" . $position . "'
                AND Genotype_with_Description = '" . $genotypeWithDescription . "'
                AND Improvement_Status LIKE '%improved%' ORDER BY Accession;
            ";
        } else if (preg_match("/landrace/i", strval($key))) {
            $sql = "
                SELECT Classification, Improvement_Status, Maturity_Group, Country, State, 
                Accession, Gene, Position, Genotype, Genotype_with_Description, Imputation 
                FROM " . $db . "." . $dataset . "
                WHERE (Gene IN ('" . $gene . "'))
                AND Position = '" . $position . "'
                AND Genotype_with_Description = '" . $genotypeWithDescription . "'
                AND Improvement_Status LIKE '%landrace%' ORDER BY Accession;
            ";
        } else if (preg_match("/other/i", strval($key))) {
            $sql = "
                SELECT Classification, Improvement_Status, Maturity_Group, Country, State, 
                Accession, Gene, Position, Genotype, Genotype_with_Description, Imputation 
                FROM " . $db . "." . $dataset . "
                WHERE (Gene IN ('" . $gene . "'))
                AND Position = '" . $position . "'
                AND Genotype_with_Description = '" . $genotypeWithDescription . "'
                AND Improvement_Status LIKE '%other%' ORDER BY Accession;
            ";
        } else if (preg_match("/total/i", strval($key))) {
            $sql = "
                SELECT Classification, Improvement_Status, Maturity_Group, Country, State, 
                Accession, Gene, Position, Genotype, Genotype_with_Description, Imputation 
                FROM " . $db . "." . $dataset . "
                WHERE (Gene IN ('" . $gene . "'))
                AND Position = '" . $position . "'
                AND Genotype_with_Description = '" . $genotypeWithDescription . "'
                ORDER BY Accession;
            ";
        } else if (preg_match("/unimputed/i", strval($key))) {
            $sql = "
                SELECT Classification, Improvement_Status, Maturity_Group, Country, State, 
                Accession, Gene, Position, Genotype, Genotype_with_Description, Imputation 
                FROM " . $db . "." . $dataset . "
                WHERE (Gene IN ('" . $gene . "'))
                AND Position = '" . $position . "'
                AND Genotype_with_Description = '" . $genotypeWithDescription . "'
                AND Imputation = '-'
                ORDER BY Accession;
            ";
        } else if (preg_match("/imputed/i", strval($key))) {
            $sql = "
                SELECT Classification, Improvement_Status, Maturity_Group, Country, State, 
                Accession, Gene, Position, Genotype, Genotype_with_Description, Imputation 
                FROM " . $db . "." . $dataset . "
                WHERE (Gene IN ('" . $gene . "'))
                AND Position = '" . $position . "'
                AND Genotype_with_Description = '" . $genotypeWithDescription . "'
                AND Imputation = '+'
                ORDER BY Accession;
            ";
        }

        $result_arr = DB::connection($db)->select($sql);

        return json_encode($result_arr);
    }


    public function DownloadAllCountsByGene(Request $request, $organism)
    {
        $gene = $request->Gene;
        $dataset = $request->Dataset;
        $organism = $request->Organism;

        // Database
        $db = "KBC_" . $organism;

        // Parse genes
        if (is_string($gene)) {
            $gene_arr = preg_split("/[;, \n]+/", trim($gene));
        } elseif (is_array($gene)) {
            $gene_arr = $gene;
        }
        for ($i = 0; $i < count($gene_arr); $i++) {
            $gene_arr[$i] = trim($gene_arr[$i]);
        }

        // Construct sql then make query
        if ($organism == "Zmays") {
            $sql = "
            SELECT COUNT(IF(Improvement_Status = 'Improved', 1, null)) AS Improved_Cultivar, 
            COUNT(IF(Improvement_Status = 'Landrace', 1, null)) AS Landrace, 
            COUNT(IF(Improvement_Status = 'Other', 1, null)) AS Other, 
            COUNT(IF(Improvement_Status IN ('Improved', 'Cultivar', 'Elite', 'Landrace', 'Genetic', 'Other') OR Improvement_Status IS NULL, 1, null)) AS Total, 
            COUNT(IF(Imputation = '+', 1, null)) AS Imputed, 
            COUNT(IF(Imputation = '-', 1, null)) AS Unimputed, 
            Gene, Position, Genotype, Genotype_with_Description 
            FROM " . $db . "." . $dataset . " 
            WHERE (Gene IN ('";
            for ($i = 0; $i < count($gene_arr); $i++) {
                if ($i < (count($gene_arr) - 1)) {
                    $sql = $sql . $gene_arr[$i] . "', '";
                } else {
                    $sql = $sql . $gene_arr[$i];
                }
            }
            $sql = $sql . "')) 
            GROUP BY Gene, Position, Genotype, Genotype_with_Description 
            ORDER BY Gene, Position, Total DESC;";
        } else {
            $sql = "
            SELECT COUNT(IF(Improvement_Status IN ('Improved', 'Cultivar', 'Elite', 'Landrace', 'Genetic', 'Other') OR Improvement_Status IS NULL, 1, null)) AS Total, 
            COUNT(IF(Imputation = '+', 1, null)) AS Imputed, 
            COUNT(IF(Imputation = '-', 1, null)) AS Unimputed, 
            Gene, Position, Genotype, Genotype_with_Description 
            FROM " . $db . "." . $dataset . " 
            WHERE (Gene IN ('";
            for ($i = 0; $i < count($gene_arr); $i++) {
                if ($i < (count($gene_arr) - 1)) {
                    $sql = $sql . $gene_arr[$i] . "', '";
                } else {
                    $sql = $sql . $gene_arr[$i];
                }
            }
            $sql = $sql . "')) 
            GROUP BY Gene, Position, Genotype, Genotype_with_Description 
            ORDER BY Gene, Position, Total DESC;";
        }
        

        $result_arr = DB::connection($db)->select($sql);

        return json_encode($result_arr);
    }

    public function DownloadAllByGene(Request $request, $organism)
    {
        $gene = $request->Gene;
        $dataset = $request->Dataset;
        $organism = $request->Organism;

        // Database
        $db = "KBC_" . $organism;

        // Parse genes
        if (is_string($gene)) {
            $gene_arr = preg_split("/[;, \n]+/", trim($gene));
        } elseif (is_array($gene)) {
            $gene_arr = $gene;
        }
        for ($i = 0; $i < count($gene_arr); $i++) {
            $gene_arr[$i] = trim($gene_arr[$i]);
        }

        // Construct sql then make query
        $sql = "SELECT Classification, Improvement_Status, Maturity_Group, Country, State, 
        Accession, Gene, Position, Genotype, Genotype_with_Description, Imputation
        FROM " . $db . "." . $dataset . "
        WHERE (Gene IN ('";
        for ($i = 0; $i < count($gene_arr); $i++) {
            if ($i < (count($gene_arr) - 1)) {
                $sql = $sql . $gene_arr[$i] . "', '";
            } else {
                $sql = $sql . $gene_arr[$i];
            }
        }
        $sql = $sql . "'));";

        $result_arr = DB::connection($db)->select($sql);

        return json_encode($result_arr);
    }

    public function DownloadAllCountsByMultipleGenes(Request $request, $organism)
    {
        $genes = $request->Genes;
        $dataset = $request->Dataset;
        $organism = $request->Organism;

        // Database
        $db = "KBC_" . $organism;

        // Parse genes
        if (is_string($genes)) {
            $gene_arr = preg_split("/[;, \n]+/", trim($genes));
        } elseif (is_array($genes)) {
            $gene_arr = $genes;
        }
        for ($i = 0; $i < count($gene_arr); $i++) {
            $gene_arr[$i] = trim($gene_arr[$i]);
        }

        // Construct sql then make query
        if ($organism == "Zmays") {
            $sql = "
            SELECT COUNT(IF(Improvement_Status = 'Improved', 1, null)) AS Improved_Cultivar, 
            COUNT(IF(Improvement_Status = 'Landrace', 1, null)) AS Landrace, 
            COUNT(IF(Improvement_Status = 'Other', 1, null)) AS Other, 
            COUNT(IF(Improvement_Status IN ('Improved', 'Cultivar', 'Elite', 'Landrace', 'Genetic', 'Other') OR Improvement_Status IS NULL, 1, null)) AS Total, 
            COUNT(IF(Imputation = '+', 1, null)) AS Imputed, 
            COUNT(IF(Imputation = '-', 1, null)) AS Unimputed, 
            Gene, Position, Genotype, Genotype_with_Description 
            FROM " . $db . "." . $dataset . " 
            WHERE (Gene IN ('";
            for ($i = 0; $i < count($gene_arr); $i++) {
                if ($i < (count($gene_arr) - 1)) {
                    $sql = $sql . $gene_arr[$i] . "', '";
                } else {
                    $sql = $sql . $gene_arr[$i];
                }
            }
            $sql = $sql . "')) 
            GROUP BY Gene, Position, Genotype, Genotype_with_Description 
            ORDER BY Gene, Position, Total DESC;";
        } else {
            $sql = "
            SELECT COUNT(IF(Improvement_Status IN ('Improved', 'Cultivar', 'Elite', 'Landrace', 'Genetic', 'Other') OR Improvement_Status IS NULL, 1, null)) AS Total, 
            COUNT(IF(Imputation = '+', 1, null)) AS Imputed, 
            COUNT(IF(Imputation = '-', 1, null)) AS Unimputed, 
            Gene, Position, Genotype, Genotype_with_Description 
            FROM " . $db . "." . $dataset . " 
            WHERE (Gene IN ('";
            for ($i = 0; $i < count($gene_arr); $i++) {
                if ($i < (count($gene_arr) - 1)) {
                    $sql = $sql . $gene_arr[$i] . "', '";
                } else {
                    $sql = $sql . $gene_arr[$i];
                }
            }
            $sql = $sql . "')) 
            GROUP BY Gene, Position, Genotype, Genotype_with_Description 
            ORDER BY Gene, Position, Total DESC;";
        }
        

        $result_arr = DB::connection($db)->select($sql);

        return json_encode($result_arr);
    }

    public function DownloadAllByMultipleGenes(Request $request, $organism)
    {
        $genes = $request->Genes;
        $dataset = $request->Dataset;
        $organism = $request->Organism;

        // Database
        $db = "KBC_" . $organism;

        // Parse genes
        if (is_string($genes)) {
            $gene_arr = preg_split("/[;, \n]+/", trim($genes));
        } elseif (is_array($genes)) {
            $gene_arr = $genes;
        }
        for ($i = 0; $i < count($gene_arr); $i++) {
            $gene_arr[$i] = trim($gene_arr[$i]);
        }

        // Construct sql then make query
        $sql = "SELECT Classification, Improvement_Status, Maturity_Group, Country, State, 
        Accession, Gene, Position, Genotype, Genotype_with_Description, Imputation
        FROM " . $db . "." . $dataset . "
        WHERE (Gene IN ('";
        for ($i = 0; $i < count($gene_arr); $i++) {
            if ($i < (count($gene_arr) - 1)) {
                $sql = $sql . $gene_arr[$i] . "', '";
            } else {
                $sql = $sql . $gene_arr[$i];
            }
        }
        $sql = $sql . "'));";

        $result_arr = DB::connection($db)->select($sql);

        return json_encode($result_arr);
    }


    public function ViewAllByAccessionAndGenePage(Request $request, $organism)
    {
        $admin_db_wapper = new DBAdminWrapperClass;

        // Database
        $db = "KBC_" . $organism;

        $dataset2 = $request->dataset2;
        $accession = $request->accession;
        $gene2 = $request->gene2;

        // Parse accessions
        $accession_arr = preg_split("/[;,\n]+/", $accession);
        for ($i = 0; $i < count($accession_arr); $i++) {
            $accession_arr[$i] = trim($accession_arr[$i]);
        }

        // Construct sql then make query
        $sql = "
        SELECT Classification, Improvement_Status, Maturity_Group, Country, State, 
        Accession, Gene, Position, Genotype, Genotype_with_Description, Imputation 
        FROM " . $db . "." . $dataset2 .
            " WHERE (Gene IN ( '" . $gene2 . "' )) AND (Accession IN ('";

        for ($i = 0; $i < count($accession_arr); $i++) {
            if ($i < (count($accession_arr) - 1)) {
                $sql = $sql . $accession_arr[$i] . "', '";
            } else {
                $sql = $sql . $accession_arr[$i];
            }
        }

        $sql = $sql . "'));";

        $result_arr = DB::connection($db)->select($sql);

        // Package variables that need to go to the view
        $info = [
            'organism' => $organism,
            'dataset2' => $dataset2,
            'accession' => $accession,
            'gene2' => $gene2,
            'result_arr' => $result_arr,
        ];

        // Return to view
        return view('system/tools/AlleleCatalogTool/viewAllByAccessionAndGene')->with('info', $info);
    }


    public function DownloadAllByAccessionsAndGene(Request $request, $organism)
    {
        $gene = $request->Gene;
        $accessions = $request->Accessions;
        $dataset = $request->Dataset;
        $organism = $request->Organism;

        // Database
        $db = "KBC_" . $organism;

        if (is_string($accessions)) {
            $accession_arr = preg_split("/[;,\n]+/", trim($accessions));
        } elseif (is_array($accessions)) {
            $accession_arr = $accessions;
        }

        // Construct sql then make query
        $sql = "
        SELECT Classification, Improvement_Status, Maturity_Group, Country, State, 
        Accession, Gene, Position, Genotype, Genotype_with_Description, Imputation 
        FROM " . $db . "." . $dataset .
            " WHERE (Gene IN ( '" . $gene . "' )) AND (Accession IN ('";

        for ($i = 0; $i < count($accession_arr); $i++) {
            if ($i < (count($accession_arr) - 1)) {
                $sql = $sql . $accession_arr[$i] . "', '";
            } else {
                $sql = $sql . $accession_arr[$i];
            }
        }

        $sql = $sql . "'));";

        $result_arr = DB::connection($db)->select($sql);

        return json_encode($result_arr);
    }
}
