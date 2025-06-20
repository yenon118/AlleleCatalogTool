<?php

namespace App\Http\Controllers\System\Tools;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;


class KBCToolsAlleleCatalogToolController extends Controller
{


	function __construct() {}


	function clean_malicious_input($in_var)
	{
		$string_max_allow_length = 2000;

		if (isset($in_var)) {
			if (!empty($in_var)) {
				// Handle if the input is a string
				if (is_string($in_var)) {
					// Truncate string if longer than the max allowed length
					if (strlen($in_var) > $string_max_allow_length) {
						$in_var = substr($in_var, 0, $string_max_allow_length);
					}
					// Remove potentially dangerous characters and strip tags
					$in_var = preg_replace('/[\[\]\{\}\(\)\\\=\/\'\"\:]/', '', $in_var);
					$in_var = strip_tags($in_var);
					// Encode special characters to prevent XSS
					$in_var = htmlspecialchars($in_var, ENT_QUOTES, 'UTF-8');
					// Trim the result
					return trim($in_var);
				}
				// Handle if the input is an array
				elseif (is_array($in_var)) {
					$out_var = [];
					foreach ($in_var as $key => $value) {
						// Recursively clean each element of the array
						$out_var[$key] = self::clean_malicious_input($value);
					}
					return $out_var;
				}
				// Handle if the input is numeric or boolean
				elseif (is_numeric($in_var) || is_bool($in_var)) {
					return $in_var;
				}
			}
		}
		return null;
	}


	public function getTableNames($organism, $dataset)
	{
		// Table names and datasets
		if ($organism == "Zmays" && $dataset == "Maize1210") {
			$key_column = "Improvement_Status";
			$gff_table = "act_Maize_AGPv3_GFF";
			$accession_mapping_table = "act_" . $dataset . "_Accession_Mapping";
			$phenotype_table = "";
			$phenotype_selection_table = "";
		} elseif ($organism == "Athaliana" && $dataset == "Arabidopsis1135") {
			$key_column = "Group";
			$gff_table = "act_Arabidopsis_TAIR10_GFF";
			$accession_mapping_table = "act_" . $dataset . "_Accession_Mapping";
			$phenotype_table = "act_" . $dataset . "_Phenotype_Data";
			$phenotype_selection_table = "act_" . $dataset . "_Phenotype_Selection";
		} elseif ($organism == "Osativa" && $dataset == "Rice166") {
			$key_column = "";
			$gff_table = "act_Rice_Nipponbare_GFF";
			$accession_mapping_table = "act_" . $dataset . "_Accession_Mapping";
			$phenotype_table = "";
			$phenotype_selection_table = "";
		} elseif ($organism == "Osativa" && $dataset == "Rice3000") {
			$key_column = "Subpopulation";
			$gff_table = "act_Rice_Nipponbare_GFF";
			$accession_mapping_table = "act_" . $dataset . "_Accession_Mapping";
			$phenotype_table = "act_" . $dataset . "_Phenotype_Data";
			$phenotype_selection_table = "act_" . $dataset . "_Phenotype_Selection";
		} elseif ($organism == "Ptrichocarpa" && $dataset == "PopulusTrichocarpa882") {
			$key_column = "";
			$gff_table = "act_Ptrichocarpa_v3_1_GFF";
			$accession_mapping_table = "act_" . $dataset . "_Accession_Mapping";
			$phenotype_table = "act_" . $dataset . "_Phenotype_Data";
			$phenotype_selection_table = "act_" . $dataset . "_Phenotype_Selection";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum400") {
			$key_column = "Improvement_Status";
			$gff_table = "act_Sbicolor_v3_1_1_GFF";
			$accession_mapping_table = "act_" . $dataset . "_Accession_Mapping";
			$phenotype_table = "act_" . $dataset . "_Phenotype_Data";
			$phenotype_selection_table = "act_" . $dataset . "_Phenotype_Selection";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum499") {
			$key_column = "Improvement_Status";
			$gff_table = "act_Sbicolor_v3_1_1_GFF";
			$accession_mapping_table = "act_" . $dataset . "_Accession_Mapping";
			$phenotype_table = "act_" . $dataset . "_Phenotype_Data";
			$phenotype_selection_table = "act_" . $dataset . "_Phenotype_Selection";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum988") {
			$key_column = "Improvement_Status";
			$gff_table = "act_Sbicolor_v5_1_GFF";
			$accession_mapping_table = "act_" . $dataset . "_Accession_Mapping";
			$phenotype_table = "act_" . $dataset . "_Phenotype_Data";
			$phenotype_selection_table = "act_" . $dataset . "_Phenotype_Selection";
		} elseif ($organism == "Pvulgaris" && $dataset == "PhaseolusVulgaris2078") {
			$key_column = "Frequency_Table";
			$gff_table = "act_Pvulgaris_v2_1_GFF";
			$accession_mapping_table = "act_" . $dataset . "_Accession_Mapping";
			$phenotype_table = "act_" . $dataset . "_Phenotype_Data";
			$phenotype_selection_table = "act_" . $dataset . "_Phenotype_Selection";
		} else {
			$key_column = "";
			$gff_table = "";
			$accession_mapping_table = $dataset;
			$phenotype_table = "";
			$phenotype_selection_table = "";
		}

		return array(
			"key_column" => $key_column,
			"gff_table" => $gff_table,
			"accession_mapping_table" => $accession_mapping_table,
			"phenotype_table" => $phenotype_table,
			"phenotype_selection_table" => $phenotype_selection_table
		);
	}


	public function getSummarizedDataQueryString($organism, $dataset, $db, $gff_table, $accession_mapping_table, $gene, $chromosome, $improvement_status_array, $having = "")
	{
		if ($organism == "Zmays" && $dataset == "Maize1210") {
			// Generate SQL string
			$query_str = "SELECT ";
			if (in_array("Improved_Cultivar", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Improved_Cultivar', 1, null)) AS Improved_Cultivar, ";
			}
			if (in_array("Landrace", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Landrace', 1, null)) AS Landrace, ";
			}
			if (in_array("Wild_Relative", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Wild_Relative', 1, null)) AS Wild_Relative, ";
			}
			if (in_array("exPVP", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'exPVP', 1, null)) AS exPVP, ";
			}
			if (in_array("Other", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Other', 1, null)) AS Other, ";
			}
			$query_str = $query_str . "COUNT(ACD.Accession) AS Total, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.Improvement_Status, GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON CAST(AM.Accession AS BINARY) = CAST(GENO.Accession AS BINARY) ";
			$query_str = $query_str . " GROUP BY AM.Improvement_Status, GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . "GROUP BY ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . $having;
			$query_str = $query_str . "ORDER BY ACD.Gene, Total DESC; ";
		} elseif ($organism == "Athaliana" && $dataset == "Arabidopsis1135") {
			// Generate SQL string
			$query_str = "SELECT ";
			if (in_array("Central", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Group = 'Central', 1, null)) AS Central, ";
			}
			if (in_array("East", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Group = 'East', 1, null)) AS East, ";
			}
			if (in_array("North", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Group = 'North', 1, null)) AS North, ";
			}
			if (in_array("South", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Group = 'South', 1, null)) AS South, ";
			}
			if (in_array("Other", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Group = 'Other', 1, null)) AS Other, ";
			}
			$query_str = $query_str . "COUNT(ACD.Accession) AS Total, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.Group, GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY AM.Group, GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . "GROUP BY ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . $having;
			$query_str = $query_str . "ORDER BY ACD.Gene, Total DESC; ";
		} elseif ($organism == "Osativa" && $dataset == "Rice166") {
			// Generate SQL string
			$query_str = "SELECT ";
			$query_str = $query_str . "COUNT(ACD.Accession) AS Total, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . "GROUP BY ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . $having;
			$query_str = $query_str . "ORDER BY ACD.Gene, Total DESC; ";
		} elseif ($organism == "Osativa" && $dataset == "Rice3000") {
			// Generate SQL string
			$query_str = "SELECT ";
			if (in_array("admix", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Subpopulation = 'admix', 1, null)) AS admix, ";
			}
			if (in_array("aro", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Subpopulation = 'aro', 1, null)) AS aro, ";
			}
			if (in_array("aus", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Subpopulation = 'aus', 1, null)) AS aus, ";
			}
			if (in_array("ind1A", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Subpopulation = 'ind1A', 1, null)) AS ind1A, ";
			}
			if (in_array("ind1B", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Subpopulation = 'ind1B', 1, null)) AS ind1B, ";
			}
			if (in_array("ind2", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Subpopulation = 'ind2', 1, null)) AS ind2, ";
			}
			if (in_array("ind3", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Subpopulation = 'ind3', 1, null)) AS ind3, ";
			}
			if (in_array("indx", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Subpopulation = 'indx', 1, null)) AS indx, ";
			}
			if (in_array("japx", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Subpopulation = 'japx', 1, null)) AS japx, ";
			}
			if (in_array("subtrop", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Subpopulation = 'subtrop', 1, null)) AS subtrop, ";
			}
			if (in_array("temp", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Subpopulation = 'temp', 1, null)) AS temp, ";
			}
			if (in_array("trop", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Subpopulation = 'trop', 1, null)) AS trop, ";
			}
			$query_str = $query_str . "COUNT(ACD.Accession) AS Total, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.Subpopulation, GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY AM.Subpopulation, GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . "GROUP BY ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . $having;
			$query_str = $query_str . "ORDER BY ACD.Gene, Total DESC; ";
		} elseif ($organism == "Ptrichocarpa" && $dataset == "PopulusTrichocarpa882") {
			// Generate SQL string
			$query_str = "SELECT ";
			$query_str = $query_str . "COUNT(ACD.Accession) AS Total, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . "GROUP BY ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . $having;
			$query_str = $query_str . "ORDER BY ACD.Gene, Total DESC; ";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum400") {
			// Generate SQL string
			$query_str = "SELECT ";
			if (in_array("Converted", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Converted', 1, null)) AS Converted, ";
			}
			if (in_array("Improved", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Improved', 1, null)) AS Improved, ";
			}
			if (in_array("Landrace", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Landrace', 1, null)) AS Landrace, ";
			}
			if (in_array("Wild", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Wild', 1, null)) AS Wild, ";
			}
			$query_str = $query_str . "COUNT(ACD.Accession) AS Total, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.Improvement_Status, GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY AM.Improvement_Status, GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . "GROUP BY ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . $having;
			$query_str = $query_str . "ORDER BY ACD.Gene, Total DESC; ";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum499") {
			// Generate SQL string
			$query_str = "SELECT ";
			if (in_array("Converted", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Converted', 1, null)) AS Converted, ";
			}
			if (in_array("Improved", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Improved', 1, null)) AS Improved, ";
			}
			if (in_array("Landrace", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Landrace', 1, null)) AS Landrace, ";
			}
			if (in_array("Wild", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Wild', 1, null)) AS Wild, ";
			}
			$query_str = $query_str . "COUNT(ACD.Accession) AS Total, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.Improvement_Status, GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY AM.Improvement_Status, GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . "GROUP BY ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . $having;
			$query_str = $query_str . "ORDER BY ACD.Gene, Total DESC; ";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum988") {
			// Generate SQL string
			$query_str = "SELECT ";
			if (in_array("Breeding", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Breeding', 1, null)) AS Breeding, ";
			}
			if (in_array("Cultivar", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Cultivar', 1, null)) AS Cultivar, ";
			}
			if (in_array("Cultivated", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Cultivated', 1, null)) AS Cultivated, ";
			}
			if (in_array("Landrace", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Landrace', 1, null)) AS Landrace, ";
			}
			if (in_array("Uncertain", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Uncertain', 1, null)) AS Uncertain, ";
			}
			if (in_array("Wild", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Improvement_Status = 'Wild', 1, null)) AS Wild, ";
			}
			$query_str = $query_str . "COUNT(ACD.Accession) AS Total, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.Improvement_Status, GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY AM.Improvement_Status, GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . "GROUP BY ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . $having;
			$query_str = $query_str . "ORDER BY ACD.Gene, Total DESC; ";
		} elseif ($organism == "Pvulgaris" && $dataset == "PhaseolusVulgaris2078") {
			// Generate SQL string
			$query_str = "SELECT ";
			if (in_array("Andean", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Frequency_Table = 'Andean', 1, null)) AS Andean, ";
			}
			if (in_array("Middle_American", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Frequency_Table = 'Middle_American', 1, null)) AS Middle_American, ";
			}
			if (in_array("Wild", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Frequency_Table = 'Wild', 1, null)) AS Wild, ";
			}
			if (in_array("Mix", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Frequency_Table = 'Mix', 1, null)) AS Mix, ";
			}
			if (in_array("Ancient", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.Frequency_Table = 'Ancient', 1, null)) AS Ancient, ";
			}
			$query_str = $query_str . "COUNT(ACD.Accession) AS Total, ";
			if (in_array("Cultivar", $improvement_status_array)) {
				$query_str = $query_str . "COUNT(IF(ACD.PI = 'Yes', 1, null)) AS Cultivar, ";
			}
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.PI, AM.Frequency_Table, GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY AM.PI, AM.Frequency_Table, GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . "GROUP BY ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description ";
			$query_str = $query_str . $having;
			$query_str = $query_str . "ORDER BY ACD.Gene, Total DESC; ";
		}

		return $query_str;
	}


	public function getDataQueryString($organism, $dataset, $db, $gff_table, $accession_mapping_table, $gene, $chromosome, $where = "")
	{
		if ($organism == "Zmays" && $dataset == "Maize1210") {
			// Generate SQL string
			$query_str = "SELECT ";
			$query_str = $query_str . "ACD.Kernel_Type, ACD.Improvement_Status, ACD.Country, ACD.State, ";
			$query_str = $query_str . "ACD.Accession, ACD.Panzea_Accession, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description, ACD.Imputation ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.Kernel_Type, AM.Improvement_Status, AM.Country, AM.State, ";
			$query_str = $query_str . " GENO.Accession, AM.Panzea_Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change, GENO.Imputation) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON CAST(AM.Accession AS BINARY) = CAST(GENO.Accession AS BINARY) ";
			$query_str = $query_str . " GROUP BY AM.Kernel_Type, AM.Improvement_Status, AM.Country, AM.State, GENO.Accession, AM.Panzea_Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . $where;
			$query_str = $query_str . "ORDER BY ACD.Gene; ";
		} elseif ($organism == "Athaliana" && $dataset == "Arabidopsis1135") {
			// Generate SQL string
			$query_str = "SELECT ";
			$query_str = $query_str . "ACD.Admixture_Group, ACD.Group, ACD.Country, ACD.State, ";
			$query_str = $query_str . "ACD.Accession, ACD.TAIR_Accession, ACD.Name, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description, ACD.Imputation ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.Admixture_Group, AM.Group, AM.Country, AM.State, ";
			$query_str = $query_str . " GENO.Accession, AM.TAIR_Accession, AM.Name, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change, GENO.Imputation) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY AM.Admixture_Group, AM.Group, AM.Country, AM.State, GENO.Accession, AM.TAIR_Accession, AM.Name, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . $where;
			$query_str = $query_str . "ORDER BY ACD.Gene; ";
		} elseif ($organism == "Osativa" && $dataset == "Rice166") {
			// Generate SQL string
			$query_str = "SELECT ";
			$query_str = $query_str . "ACD.Accession, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description, ACD.Imputation ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT ";
			$query_str = $query_str . " GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change, GENO.Imputation) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . $where;
			$query_str = $query_str . "ORDER BY ACD.Gene; ";
		} elseif ($organism == "Osativa" && $dataset == "Rice3000") {
			// Generate SQL string
			$query_str = "SELECT ";
			$query_str = $query_str . "ACD.Subpopulation, ACD.Country, ";
			$query_str = $query_str . "ACD.Accession, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description, ACD.Imputation ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.Subpopulation, AM.Country, ";
			$query_str = $query_str . " GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change, GENO.Imputation) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY AM.Subpopulation, AM.Country, GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . $where;
			$query_str = $query_str . "ORDER BY ACD.Gene; ";
		} elseif ($organism == "Ptrichocarpa" && $dataset == "PopulusTrichocarpa882") {
			// Generate SQL string
			$query_str = "SELECT ";
			$query_str = $query_str . "ACD.Accession, ACD.CBI_Coding_ID, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description, ACD.Imputation ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT ";
			$query_str = $query_str . " GENO.Accession, AM.CBI_Coding_ID, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change, GENO.Imputation) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY GENO.Accession, AM.CBI_Coding_ID, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . $where;
			$query_str = $query_str . "ORDER BY ACD.Gene; ";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum400") {
			// Generate SQL string
			$query_str = "SELECT ";
			$query_str = $query_str . "ACD.Name, ACD.PI_Number, ACD.Origin, ACD.Race, ACD.Improvement_Status, ";
			$query_str = $query_str . "ACD.Accession, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description, ACD.Imputation ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.Name, AM.PI_Number, AM.Origin, AM.Race, AM.Improvement_Status, ";
			$query_str = $query_str . " GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change, GENO.Imputation) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY AM.Name, AM.PI_Number, AM.Origin, AM.Race, AM.Improvement_Status, GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . $where;
			$query_str = $query_str . "ORDER BY ACD.Gene; ";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum499") {
			// Generate SQL string
			$query_str = "SELECT ";
			$query_str = $query_str . "ACD.Name, ACD.PI_Number, ACD.Origin, ACD.Race, ACD.Improvement_Status, ";
			$query_str = $query_str . "ACD.Accession, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description, ACD.Imputation ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.Name, AM.PI_Number, AM.Origin, AM.Race, AM.Improvement_Status, ";
			$query_str = $query_str . " GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change, GENO.Imputation) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY AM.Name, AM.PI_Number, AM.Origin, AM.Race, AM.Improvement_Status, GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . $where;
			$query_str = $query_str . "ORDER BY ACD.Gene; ";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum988") {
			// Generate SQL string
			$query_str = "SELECT ";
			$query_str = $query_str . "ACD.Name, ACD.PI_Number, ACD.Origin, ACD.Race, ACD.Improvement_Status, ";
			$query_str = $query_str . "ACD.Accession, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description, ACD.Imputation ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.Name, AM.PI_Number, AM.Origin, AM.Race, AM.Improvement_Status, ";
			$query_str = $query_str . " GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change, GENO.Imputation) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY AM.Name, AM.PI_Number, AM.Origin, AM.Race, AM.Improvement_Status, GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . $where;
			$query_str = $query_str . "ORDER BY ACD.Gene; ";
		} elseif ($organism == "Pvulgaris" && $dataset == "PhaseolusVulgaris2078") {
			// Generate SQL string
			$query_str = "SELECT ";
			$query_str = $query_str . "ACD.SRA_Study, ACD.SRA_Run, ACD.SRA_Run2, ACD.Country, ACD.Continent, ACD.Organism, ACD.Improvement_Status, ACD.Population, ACD.PI, ACD.Frequency_Table, ACD.Alternate_Name, ";
			$query_str = $query_str . "ACD.Accession, ";
			$query_str = $query_str . "ACD.Gene, ACD.Chromosome, ACD.Position, ACD.Genotype, ACD.Genotype_Description, ACD.Imputation ";
			$query_str = $query_str . "FROM ( ";
			$query_str = $query_str . " SELECT AM.SRA_Study, AM.SRA_Run, AM.SRA_Run2, AM.Country, AM.Continent, AM.Organism, AM.Improvement_Status, AM.Population, AM.PI, AM.Frequency_Table, AM.Alternate_Name, ";
			$query_str = $query_str . " GENO.Accession, ";
			$query_str = $query_str . "	COMB1.Gene, GENO.Chromosome, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Position ORDER BY GENO.Position ASC SEPARATOR ' ') AS Position, ";
			$query_str = $query_str . "	GROUP_CONCAT(GENO.Genotype ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype, ";
			$query_str = $query_str . "	GROUP_CONCAT(CONCAT_WS('|', GENO.Genotype, IFNULL( FUNC2.Functional_Effect, GENO.Category ), FUNC2.Amino_Acid_Change, GENO.Imputation) ORDER BY GENO.Position ASC SEPARATOR ' ') AS Genotype_Description, ";
			$query_str = $query_str . "	GROUP_CONCAT(IFNULL(GENO.Imputation, '-') ORDER BY GENO.Position ASC SEPARATOR ' ') AS Imputation ";
			$query_str = $query_str . "	FROM ( ";
			$query_str = $query_str . "		SELECT DISTINCT FUNC.Chromosome, FUNC.Position, GFF.ID As Gene ";
			$query_str = $query_str . "		FROM " . $db . "." . $gff_table . " AS GFF ";
			$query_str = $query_str . "		INNER JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC ";
			$query_str = $query_str . "		ON (FUNC.Chromosome = GFF.Chromosome) AND (FUNC.Position >= GFF.Start) AND (FUNC.Position <= GFF.End) ";
			$query_str = $query_str . "		WHERE (GFF.ID=\"" . $gene . "\") AND (GFF.Feature=\"gene\") AND (FUNC.Gene_Name LIKE '%" . $gene . "%') AND (FUNC.Chromosome=\"" . $chromosome . "\") ";
			$query_str = $query_str . "	) AS COMB1 ";
			$query_str = $query_str . "	INNER JOIN " . $db . ".act_" . $dataset . "_genotype_" . $chromosome . " AS GENO ";
			$query_str = $query_str . "	ON (GENO.Chromosome = COMB1.Chromosome) AND (GENO.Position = COMB1.Position) ";
			$query_str = $query_str . "	LEFT JOIN " . $db . ".act_" . $dataset . "_func_eff_" . $chromosome . " AS FUNC2 ";
			$query_str = $query_str . "	ON (FUNC2.Chromosome = GENO.Chromosome) AND (FUNC2.Position = GENO.Position) AND (FUNC2.Allele = GENO.Genotype) AND (FUNC2.Gene LIKE CONCAT('%', COMB1.Gene, '%')) ";
			$query_str = $query_str . " LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
			$query_str = $query_str . " ON AM.Accession = GENO.Accession ";
			$query_str = $query_str . " GROUP BY AM.SRA_Study, AM.SRA_Run, AM.SRA_Run2, AM.Country, AM.Continent, AM.Organism, AM.Improvement_Status, AM.Population, AM.PI, AM.Frequency_Table, AM.Alternate_Name, GENO.Accession, COMB1.Gene, GENO.Chromosome ";
			$query_str = $query_str . ") AS ACD ";
			$query_str = $query_str . $where;
			$query_str = $query_str . "ORDER BY ACD.Gene; ";
		}

		return $query_str;
	}


	public function AlleleCatalogToolPage(Request $request, $organism)
	{
		$organism = preg_replace('/\s+/', '', $organism);

		// Database
		$db = "KBC_" . $organism;

		// Table names and datasets
		if ($organism == "Zmays") {
			$gff_table = "act_Maize_AGPv3_GFF";
			$accession_mapping_table = "act_Maize1210_Accession_Mapping";
		} elseif ($organism == "Athaliana") {
			$gff_table = "act_Arabidopsis_TAIR10_GFF";
			$accession_mapping_table = "act_Arabidopsis1135_Accession_Mapping";
		} elseif ($organism == "Osativa") {
			$gff_table = "act_Rice_Nipponbare_GFF";
			$accession_mapping_table = "act_Rice3000_Accession_Mapping";
		} elseif ($organism == "Ptrichocarpa") {
			$gff_table = "act_Ptrichocarpa_v3_1_GFF";
			$accession_mapping_table = "act_PopulusTrichocarpa882_Accession_Mapping";
		} elseif ($organism == "Sbicolor") {
			$gff_table = "act_Sbicolor_v3_1_1_GFF";
			$accession_mapping_table = "act_Sorghum400_Accession_Mapping";
		} elseif ($organism == "Pvulgaris") {
			$gff_table = "act_Pvulgaris_v2_1_GFF";
			$accession_mapping_table = "act_PhaseolusVulgaris2078_Accession_Mapping";
		}

		// Define datasets
		if ($organism == "Zmays") {
			$dataset_array = array("Maize1210");
		} elseif ($organism == "Athaliana") {
			$dataset_array = array("Arabidopsis1135");
		} elseif ($organism == "Osativa") {
			$dataset_array = array("Rice3000", "Rice166");
		} elseif ($organism == "Ptrichocarpa") {
			$dataset_array = array("PopulusTrichocarpa882");
		} elseif ($organism == "Sbicolor") {
			$dataset_array = array("Sorghum400", "Sorghum499", "Sorghum988");
		} elseif ($organism == "Pvulgaris") {
			$dataset_array = array("PhaseolusVulgaris2078");
		}

		try {
			// Query gene from database
			if ($organism == "Zmays") {
				$sql = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL AND Name LIKE 'GRMZM%' LIMIT 3;";
			} elseif ($organism == "Ptrichocarpa") {
				$sql = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL LIMIT 3,3;";
			} elseif ($organism == "Sbicolor") {
				$sql = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL LIMIT 3,3;";
			} else {
				$sql = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL LIMIT 3;";
			}
			$gene_array = DB::connection($db)->select($sql);


			// Query improvement status, group, or subpopulation from database
			if ($organism == "Zmays") {
				$key_column = "Improvement_Status";
				$sql = "SELECT DISTINCT Improvement_Status AS `Key` FROM " . $db . "." . $accession_mapping_table . ";";
				$improvement_status_array = DB::connection($db)->select($sql);
			} elseif ($organism == "Athaliana") {
				$key_column = "Group";
				$sql = "SELECT DISTINCT `Group` AS `Key` FROM " . $db . "." . $accession_mapping_table . ";";
				$improvement_status_array = DB::connection($db)->select($sql);
			} elseif ($organism == "Osativa") {
				$key_column = "Subpopulation";
				$sql = "SELECT DISTINCT Subpopulation AS `Key` FROM " . $db . "." . $accession_mapping_table . ";";
				$improvement_status_array = DB::connection($db)->select($sql);
			} elseif ($organism == "Sbicolor") {
				$key_column = "Improvement_Status";
				$sql = "SELECT DISTINCT Improvement_Status AS `Key` FROM " . $db . "." . $accession_mapping_table . " WHERE Improvement_Status IS NOT NULL;";
				$improvement_status_array = DB::connection($db)->select($sql);
			} elseif ($organism == "Pvulgaris") {
				$key_column = "Frequency_Table";
				$sql = "SELECT DISTINCT Frequency_Table AS `Key` FROM " . $db . "." . $accession_mapping_table . " WHERE Frequency_Table IS NOT NULL;";
				$improvement_status_array = DB::connection($db)->select($sql);
				$improvement_status_array[] = (object) ['Key' => 'Cultivar'];
			} else {
				$key_column = "";
				$improvement_status_array = array();
			}


			// Query accession from database
			if ($organism == "Zmays") {
				$sql = "SELECT DISTINCT Panzea_Accession AS Accession FROM " . $db . "." . $accession_mapping_table . " WHERE Accession IS NOT NULL LIMIT 3;";
			} elseif ($organism == "Athaliana") {
				$sql = "SELECT DISTINCT TAIR_Accession AS Accession FROM " . $db . "." . $accession_mapping_table . " WHERE Accession IS NOT NULL LIMIT 3;";
			} elseif ($organism == "Osativa") {
				$sql = "SELECT DISTINCT Accession FROM " . $db . "." . $accession_mapping_table . " WHERE Accession IS NOT NULL LIMIT 3;";
			} elseif ($organism == "Ptrichocarpa") {
				$sql = "SELECT DISTINCT Accession FROM " . $db . "." . $accession_mapping_table . " WHERE Accession IS NOT NULL LIMIT 1,3;";
			} elseif ($organism == "Sbicolor") {
				$sql = "SELECT DISTINCT Accession FROM " . $db . "." . $accession_mapping_table . " WHERE Accession IS NOT NULL LIMIT 3;";
			} elseif ($organism == "Pvulgaris") {
				$sql = "SELECT DISTINCT Accession FROM " . $db . "." . $accession_mapping_table . " WHERE Accession IS NOT NULL LIMIT 3;";
			}
			$accession_array = DB::connection($db)->select($sql);


			// Package variables that need to go to the view
			$info = [
				'organism' => $organism,
				'dataset_array' => $dataset_array,
				'gene_array' => $gene_array,
				'accession_array' => $accession_array,
				'key_column' => $key_column,
				'improvement_status_array' => $improvement_status_array,
				'accession_mapping_table' => $accession_mapping_table
			];

			// Return to view
			return view('system/tools/AlleleCatalogTool/AlleleCatalogToolNew')->with('info', $info);
		} catch (\Throwable $e) {
			// Package variables that need to go to the view
			$info = [
				'organism' => $organism
			];

			// Return to view
			return view('system/tools/AlleleCatalogTool/AlleleCatalogToolNotAvailable')->with('info', $info);
		}
	}


	public function UpdateSearchByGeneIDs(Request $request, $organism)
	{
		$organism = preg_replace('/\s+/', '', $organism);

		// Database
		$db = "KBC_" . $organism;

		$dataset = $request->Dataset;

		$dataset = self::clean_malicious_input($dataset);
		$dataset = preg_replace('/\s+/', '', $dataset);

		// Table names and datasets
		$table_names = self::getTableNames($organism, $dataset);
		$key_column = $table_names["key_column"];
		$gff_table = $table_names["gff_table"];
		$accession_mapping_table = $table_names["accession_mapping_table"];

		// Query gene from database
		if ($organism == "Zmays" && $dataset == "Maize1210") {
			$sql = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL AND Name LIKE 'GRMZM%' LIMIT 3;";
		} elseif ($organism == "Ptrichocarpa" && $dataset == "PopulusTrichocarpa882") {
			$sql = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL LIMIT 3,3;";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum499") {
			$sql = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL LIMIT 3,3;";
		} else {
			$sql = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL LIMIT 3;";
		}
		$gene_array = DB::connection($db)->select($sql);

		// Query improvement status, group, or subpopulation from database
		if ($organism == "Zmays" && $dataset == "Maize1210") {
			$sql = "SELECT DISTINCT Improvement_Status AS `Key` FROM " . $db . "." . $accession_mapping_table . ";";
		} elseif ($organism == "Athaliana" && $dataset == "Arabidopsis1135") {
			$sql = "SELECT DISTINCT `Group` AS `Key` FROM " . $db . "." . $accession_mapping_table . ";";
		} elseif ($organism == "Osativa" && $dataset == "Rice3000") {
			$sql = "SELECT DISTINCT Subpopulation AS `Key` FROM " . $db . "." . $accession_mapping_table . ";";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum400") {
			$sql = "SELECT DISTINCT Improvement_Status AS `Key` FROM " . $db . "." . $accession_mapping_table . " WHERE Improvement_Status IS NOT NULL;";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum499") {
			$sql = "SELECT DISTINCT Improvement_Status AS `Key` FROM " . $db . "." . $accession_mapping_table . " WHERE Improvement_Status IS NOT NULL;";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum988") {
			$sql = "SELECT DISTINCT Improvement_Status AS `Key` FROM " . $db . "." . $accession_mapping_table . " WHERE Improvement_Status IS NOT NULL;";
		} elseif ($organism == "Pvulgaris" && $dataset == "PhaseolusVulgaris2078") {
			$sql = "SELECT DISTINCT Frequency_Table AS `Key` FROM " . $db . "." . $accession_mapping_table . " WHERE Frequency_Table IS NOT NULL;";
		} else {
			$sql = "";
		}
		try {
			$improvement_status_array = DB::connection($db)->select($sql);
		} catch (\Throwable $e) {
			$improvement_status_array = array();
		}
		if ($organism == "Pvulgaris" && $dataset == "PhaseolusVulgaris2078") {
		    $improvement_status_array[] = (object) ['Key' => 'Cultivar'];
		}

		$result_arr = [
			"Gene" => $gene_array,
			"Key_Column" => $key_column,
			"Improvement_Status" => $improvement_status_array
		];

		return json_encode($result_arr);
	}


	public function UpdateSearchByAccessionsandGeneID(Request $request, $organism)
	{
		$organism = preg_replace('/\s+/', '', $organism);

		// Database
		$db = "KBC_" . $organism;

		$dataset = $request->Dataset;

		$dataset = self::clean_malicious_input($dataset);
		$dataset = preg_replace('/\s+/', '', $dataset);

		// Table names and datasets
		$table_names = self::getTableNames($organism, $dataset);
		$key_column = $table_names["key_column"];
		$gff_table = $table_names["gff_table"];
		$accession_mapping_table = $table_names["accession_mapping_table"];

		// Query gene from database
		if ($organism == "Zmays" && $dataset == "Maize1210") {
			$sql = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL AND Name LIKE 'GRMZM%' LIMIT 3;";
		} elseif ($organism == "Ptrichocarpa" && $dataset == "PopulusTrichocarpa882") {
			$sql = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL LIMIT 3,3;";
		} elseif ($organism == "Sbicolor" && $dataset == "Sorghum499") {
			$sql = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL LIMIT 3,3;";
		} else {
			$sql = "SELECT DISTINCT Name AS Gene FROM " . $db . "." . $gff_table . " WHERE Name IS NOT NULL LIMIT 3;";
		}
		$gene_array = DB::connection($db)->select($sql);

		// Query accession from database
		if ($organism == "Zmays" && $dataset == "Maize1210") {
			$sql = "SELECT DISTINCT Panzea_Accession AS Accession FROM " . $db . "." . $accession_mapping_table . " WHERE Accession IS NOT NULL LIMIT 3;";
		} elseif ($organism == "Athaliana" && $dataset == "Arabidopsis1135") {
			$sql = "SELECT DISTINCT TAIR_Accession AS Accession FROM " . $db . "." . $accession_mapping_table . " WHERE Accession IS NOT NULL LIMIT 3;";
		} else {
			$sql = "SELECT DISTINCT Accession FROM " . $db . "." . $accession_mapping_table . " WHERE Accession IS NOT NULL LIMIT 3;";
		}
		$accession_array = DB::connection($db)->select($sql);

		$result_arr = [
			"Gene" => $gene_array,
			"Accession" => $accession_array,
		];

		return json_encode($result_arr);
	}


	public function QueryAccessionInformation(Request $request, $organism)
	{
		$organism = preg_replace('/\s+/', '', $organism);

		// Database
		$db = "KBC_" . $organism;

		$dataset = $request->Dataset;

		$dataset = self::clean_malicious_input($dataset);
		$dataset = preg_replace('/\s+/', '', $dataset);

		// Table names and datasets
		$table_names = self::getTableNames($organism, $dataset);
		$key_column = $table_names["key_column"];
		$gff_table = $table_names["gff_table"];
		$accession_mapping_table = $table_names["accession_mapping_table"];

		// Query string
		$query_str = "SELECT * FROM " . $db . "." . $accession_mapping_table . ";";

		$result_arr = DB::connection($db)->select($query_str);

		return json_encode($result_arr);
	}


	public function ViewAllByGenesPage(Request $request, $organism)
	{
		try {
			$organism = preg_replace('/\s+/', '', $organism);

			// Database
			$db = "KBC_" . $organism;

			$query_str = "SET SESSION group_concat_max_len = 1000000; ";
			$set_group_concat_max_len_result = DB::connection($db)->select($query_str);

			$dataset = $request->dataset_1;
			$gene = $request->gene_1;
			$improvement_status = $request->improvement_status_1;

			$dataset = self::clean_malicious_input($dataset);
			$dataset = preg_replace('/\s+/', '', $dataset);

			$gene = self::clean_malicious_input($gene);

			$improvement_status = self::clean_malicious_input($improvement_status);

			$gene_array = array();
			if (is_string($gene)) {
				$gene = trim($gene);
				$temp_gene_array = preg_split("/[;, \n]+/", $gene);
				$gene_array = array();
				for ($i = 0; $i < count($temp_gene_array); $i++) {
					if (!empty(preg_replace('/\s+/', '', $temp_gene_array[$i]))) {
						array_push($gene_array, preg_replace('/\s+/', '', $temp_gene_array[$i]));
					}
				}
			} elseif (is_array($gene)) {
				$temp_gene_array = $gene;
				$gene_array = array();
				for ($i = 0; $i < count($temp_gene_array); $i++) {
					if (!empty(preg_replace('/\s+/', '', $temp_gene_array[$i]))) {
						array_push($gene_array, preg_replace('/\s+/', '', $temp_gene_array[$i]));
					}
				}
			}

			if (isset($improvement_status)) {
				if (is_string($improvement_status)) {
					$improvement_status = trim($improvement_status);
					$temp_improvement_status_array = preg_split("/[;, \n]+/", $improvement_status);
					$improvement_status_array = array();
					for ($i = 0; $i < count($temp_improvement_status_array); $i++) {
						if (!empty(trim($temp_improvement_status_array[$i]))) {
							array_push($improvement_status_array, trim($temp_improvement_status_array[$i]));
						}
					}
				} elseif (is_array($improvement_status)) {
					$temp_improvement_status_array = $improvement_status;
					$improvement_status_array = array();
					for ($i = 0; $i < count($temp_improvement_status_array); $i++) {
						if (!empty(trim($temp_improvement_status_array[$i]))) {
							array_push($improvement_status_array, trim($temp_improvement_status_array[$i]));
						}
					}
				}
			} else {
				$improvement_status_array = array();
			}

			// Table names and datasets
			$table_names = self::getTableNames($organism, $dataset);
			$key_column = $table_names["key_column"];
			$gff_table = $table_names["gff_table"];
			$accession_mapping_table = $table_names["accession_mapping_table"];

			$gene_result_arr = array();
			$allele_catalog_result_arr = array();

			for ($i = 0; $i < count($gene_array); $i++) {

				try {
					// Generate SQL string
					$query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
					$query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
					$query_str = $query_str . "WHERE Name IN ('" . $gene_array[$i] . "');";

					$temp_gene_result_arr = DB::connection($db)->select($query_str);

					// Generate SQL string
					$query_str = self::getSummarizedDataQueryString(
						$organism,
						$dataset,
						$db,
						$gff_table,
						$accession_mapping_table,
						$gene_array[$i],
						$temp_gene_result_arr[0]->Chromosome,
						$improvement_status_array,
						""
					);

					$result_arr = DB::connection($db)->select($query_str);

					array_push($gene_result_arr, $temp_gene_result_arr);
					array_push($allele_catalog_result_arr, $result_arr);
				} catch (\Throwable $e) {
				}
			}

			// Package variables that need to go to the view
			$info = [
				'organism' => $organism,
				'dataset' => $dataset,
				'gene_array' => $gene_array,
				'improvement_status_array' => $improvement_status_array,
				'gene_result_arr' => $gene_result_arr,
				'allele_catalog_result_arr' => $allele_catalog_result_arr
			];

			// Return to view
			return view('system/tools/AlleleCatalogTool/viewAllByGenes')->with('info', $info);
		} catch (\Throwable $e) {
			abort(500);
		}
	}


	public function QueryMetadataByImprovementStatusAndGenotypeCombination(Request $request, $organism)
	{
		$organism = preg_replace('/\s+/', '', $organism);

		// Database
		$db = "KBC_" . $organism;

		$query_str = "SET SESSION group_concat_max_len = 1000000; ";
		$set_group_concat_max_len_result = DB::connection($db)->select($query_str);

		$organism = $request->Organism;
		$dataset = $request->Dataset;
		$key = $request->Key;
		$gene = $request->Gene;
		$chromosome = $request->Chromosome;
		$position = $request->Position;
		$genotype = $request->Genotype;
		$genotype_description = $request->Genotype_Description;

		$organism = self::clean_malicious_input($organism);
		$organism = preg_replace('/\s+/', '', $organism);

		$dataset = self::clean_malicious_input($dataset);
		$dataset = preg_replace('/\s+/', '', $dataset);

		$key = self::clean_malicious_input($key);
		$key = preg_replace('/\s+/', '', $key);

		$gene = self::clean_malicious_input($gene);
		$gene = preg_replace('/\s+/', '', $gene);

		$chromosome = self::clean_malicious_input($chromosome);
		$chromosome = preg_replace('/\s+/', '', $chromosome);

		$position = self::clean_malicious_input($position);

		$genotype = self::clean_malicious_input($genotype);

		$genotype_description = self::clean_malicious_input($genotype_description);

		// Table names and datasets
		$table_names = self::getTableNames($organism, $dataset);
		$key_column = $table_names["key_column"];
		$gff_table = $table_names["gff_table"];
		$accession_mapping_table = $table_names["accession_mapping_table"];

        if ($organism == "Pvulgaris" && $dataset == "PhaseolusVulgaris2078" && $key == "Cultivar") {
            $key = "Yes";
            $key_column = "PI";
        }

		// Generate SQL string
		$query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
		$query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
		$query_str = $query_str . "WHERE Name IN ('" . $gene . "');";

		$gene_result_arr = DB::connection($db)->select($query_str);

		// Generate SQL string
		if ($key == "Total") {
			$query_str = "WHERE (ACD.Position = '" . $position . "') AND (ACD.Genotype = '" . $genotype . "')";
		} else {
			$query_str = "WHERE ";
			$query_str = $query_str . "(ACD." . $key_column . " = '" . $key . "') AND ";
			$query_str = $query_str . "(ACD.Position = '" . $position . "') AND ";
			$query_str = $query_str . "(ACD.Genotype = '" . $genotype . "')";
		}

		$query_str = self::getDataQueryString(
			$organism,
			$dataset,
			$db,
			$gff_table,
			$accession_mapping_table,
			$gene,
			$gene_result_arr[0]->Chromosome,
			$query_str
		);

		$result_arr = DB::connection($db)->select($query_str);

		return json_encode($result_arr);
	}


	public function QueryAllCountsByGene(Request $request, $organism)
	{
		$organism = preg_replace('/\s+/', '', $organism);

		// Database
		$db = "KBC_" . $organism;

		$query_str = "SET SESSION group_concat_max_len = 1000000; ";
		$set_group_concat_max_len_result = DB::connection($db)->select($query_str);

		$dataset = $request->Dataset;
		$gene = $request->Gene;
		$improvement_status = $request->Improvement_Status_Array;

		$dataset = self::clean_malicious_input($dataset);
		$dataset = preg_replace('/\s+/', '', $dataset);

		$gene = self::clean_malicious_input($gene);
		$gene = preg_replace('/\s+/', '', $gene);

		$improvement_status = self::clean_malicious_input($improvement_status);

		$improvement_status_array = array();
		if (isset($improvement_status)) {
			if (is_string($improvement_status)) {
				$improvement_status = trim($improvement_status);
				$temp_improvement_status_array = preg_split("/[;, \n]+/", $improvement_status);
				$improvement_status_array = array();
				for ($i = 0; $i < count($temp_improvement_status_array); $i++) {
					if (!empty(trim($temp_improvement_status_array[$i]))) {
						array_push($improvement_status_array, trim($temp_improvement_status_array[$i]));
					}
				}
			} elseif (is_array($improvement_status)) {
				$temp_improvement_status_array = $improvement_status;
				$improvement_status_array = array();
				for ($i = 0; $i < count($temp_improvement_status_array); $i++) {
					if (!empty(trim($temp_improvement_status_array[$i]))) {
						array_push($improvement_status_array, trim($temp_improvement_status_array[$i]));
					}
				}
			}
		} else {
			$improvement_status_array = array();
		}

		// Table names and datasets
		$table_names = self::getTableNames($organism, $dataset);
		$key_column = $table_names["key_column"];
		$gff_table = $table_names["gff_table"];
		$accession_mapping_table = $table_names["accession_mapping_table"];

		// Generate SQL string
		$query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
		$query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
		$query_str = $query_str . "WHERE Name IN ('" . $gene . "');";

		$gene_result_arr = DB::connection($db)->select($query_str);

		$query_str = self::getSummarizedDataQueryString(
			$organism,
			$dataset,
			$db,
			$gff_table,
			$accession_mapping_table,
			$gene,
			$gene_result_arr[0]->Chromosome,
			$improvement_status_array,
			""
		);

		$result_arr = DB::connection($db)->select($query_str);

		return json_encode($result_arr);
	}


	public function QueryAllByGene(Request $request, $organism)
	{
		$organism = preg_replace('/\s+/', '', $organism);

		// Database
		$db = "KBC_" . $organism;

		$dataset = $request->Dataset;
		$gene = $request->Gene;
		$improvement_status = $request->Improvement_Status_Array;

		$dataset = self::clean_malicious_input($dataset);
		$dataset = preg_replace('/\s+/', '', $dataset);

		$gene = self::clean_malicious_input($gene);
		$gene = preg_replace('/\s+/', '', $gene);

		$improvement_status = self::clean_malicious_input($improvement_status);

		if (is_string($improvement_status)) {
			$improvement_status = trim($improvement_status);
			$temp_improvement_status_array = preg_split("/[;, \n]+/", $improvement_status);
			$improvement_status_array = array();
			for ($i = 0; $i < count($temp_improvement_status_array); $i++) {
				if (!empty(trim($temp_improvement_status_array[$i]))) {
					array_push($improvement_status_array, trim($temp_improvement_status_array[$i]));
				}
			}
		} elseif (is_array($improvement_status)) {
			$temp_improvement_status_array = $improvement_status;
			$improvement_status_array = array();
			for ($i = 0; $i < count($temp_improvement_status_array); $i++) {
				if (!empty(trim($temp_improvement_status_array[$i]))) {
					array_push($improvement_status_array, trim($temp_improvement_status_array[$i]));
				}
			}
		}

		// Table names and datasets
		$table_names = self::getTableNames($organism, $dataset);
		$key_column = $table_names["key_column"];
		$gff_table = $table_names["gff_table"];
		$accession_mapping_table = $table_names["accession_mapping_table"];

		// Generate SQL string
		$query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
		$query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
		$query_str = $query_str . "WHERE Name IN ('" . $gene . "');";

		$gene_result_arr = DB::connection($db)->select($query_str);

		$query_str = self::getDataQueryString(
			$organism,
			$dataset,
			$db,
			$gff_table,
			$accession_mapping_table,
			$gene,
			$gene_result_arr[0]->Chromosome,
			""
		);

		$result_arr = DB::connection($db)->select($query_str);

		for ($i = 0; $i < count($result_arr); $i++) {
			if (preg_match("/\+/i", $result_arr[$i]->Imputation)) {
				$result_arr[$i]->Imputation = "+";
			} else {
				$result_arr[$i]->Imputation = "";
			}
		}

		return json_encode($result_arr);
	}


	public function QueryAllCountsByMultipleGenes(Request $request, $organism)
	{
		$organism = preg_replace('/\s+/', '', $organism);

		// Database
		$db = "KBC_" . $organism;

		$query_str = "SET SESSION group_concat_max_len = 1000000; ";
		$set_group_concat_max_len_result = DB::connection($db)->select($query_str);

		$dataset = $request->Dataset;
		$gene = $request->Gene_Array;
		$improvement_status = $request->Improvement_Status_Array;

		$dataset = self::clean_malicious_input($dataset);
		$dataset = preg_replace('/\s+/', '', $dataset);

		$gene = self::clean_malicious_input($gene);

		$improvement_status = self::clean_malicious_input($improvement_status);

		if (is_string($gene)) {
			$gene = trim($gene);
			$temp_gene_array = preg_split("/[;, \n]+/", $gene);
			$gene_array = array();
			for ($i = 0; $i < count($temp_gene_array); $i++) {
				if (!empty(preg_replace('/\s+/', '', $temp_gene_array[$i]))) {
					array_push($gene_array, preg_replace('/\s+/', '', $temp_gene_array[$i]));
				}
			}
		} elseif (is_array($gene)) {
			$temp_gene_array = $gene;
			$gene_array = array();
			for ($i = 0; $i < count($temp_gene_array); $i++) {
				if (!empty(preg_replace('/\s+/', '', $temp_gene_array[$i]))) {
					array_push($gene_array, preg_replace('/\s+/', '', $temp_gene_array[$i]));
				}
			}
		}

		if (is_string($improvement_status)) {
			$improvement_status = trim($improvement_status);
			$temp_improvement_status_array = preg_split("/[;, \n]+/", $improvement_status);
			$improvement_status_array = array();
			for ($i = 0; $i < count($temp_improvement_status_array); $i++) {
				if (!empty(trim($temp_improvement_status_array[$i]))) {
					array_push($improvement_status_array, trim($temp_improvement_status_array[$i]));
				}
			}
		} elseif (is_array($improvement_status)) {
			$temp_improvement_status_array = $improvement_status;
			$improvement_status_array = array();
			for ($i = 0; $i < count($temp_improvement_status_array); $i++) {
				if (!empty(trim($temp_improvement_status_array[$i]))) {
					array_push($improvement_status_array, trim($temp_improvement_status_array[$i]));
				}
			}
		}

		// Table names and datasets
		$table_names = self::getTableNames($organism, $dataset);
		$key_column = $table_names["key_column"];
		$gff_table = $table_names["gff_table"];
		$accession_mapping_table = $table_names["accession_mapping_table"];

		for ($i = 0; $i < count($gene_array); $i++) {

			// Generate SQL string
			$query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
			$query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
			$query_str = $query_str . "WHERE Name IN ('" . $gene_array[$i] . "');";

			$temp_gene_result_arr = DB::connection($db)->select($query_str);

			$query_str = self::getSummarizedDataQueryString(
				$organism,
				$dataset,
				$db,
				$gff_table,
				$accession_mapping_table,
				$gene_array[$i],
				$temp_gene_result_arr[0]->Chromosome,
				$improvement_status_array,
				""
			);

			$result_arr = DB::connection($db)->select($query_str);

			if (!isset($allele_catalog_result_arr)) {
				$allele_catalog_result_arr = (array) $result_arr;
			} else {
				$allele_catalog_result_arr = array_merge($allele_catalog_result_arr, (array) $result_arr);
			}
		}

		return json_encode($allele_catalog_result_arr);
	}


	public function QueryAllByMultipleGenes(Request $request, $organism)
	{
		$organism = preg_replace('/\s+/', '', $organism);

		// Database
		$db = "KBC_" . $organism;

		$dataset = $request->Dataset;
		$gene = $request->Gene_Array;
		$improvement_status = $request->Improvement_Status_Array;

		$dataset = self::clean_malicious_input($dataset);
		$dataset = preg_replace('/\s+/', '', $dataset);

		$gene = self::clean_malicious_input($gene);

		$improvement_status = self::clean_malicious_input($improvement_status);

		if (is_string($gene)) {
			$gene = trim($gene);
			$temp_gene_array = preg_split("/[;, \n]+/", $gene);
			$gene_array = array();
			for ($i = 0; $i < count($temp_gene_array); $i++) {
				if (!empty(preg_replace('/\s+/', '', $temp_gene_array[$i]))) {
					array_push($gene_array, preg_replace('/\s+/', '', $temp_gene_array[$i]));
				}
			}
		} elseif (is_array($gene)) {
			$temp_gene_array = $gene;
			$gene_array = array();
			for ($i = 0; $i < count($temp_gene_array); $i++) {
				if (!empty(preg_replace('/\s+/', '', $temp_gene_array[$i]))) {
					array_push($gene_array, preg_replace('/\s+/', '', $temp_gene_array[$i]));
				}
			}
		}

		if (is_string($improvement_status)) {
			$improvement_status = trim($improvement_status);
			$temp_improvement_status_array = preg_split("/[;, \n]+/", $improvement_status);
			$improvement_status_array = array();
			for ($i = 0; $i < count($temp_improvement_status_array); $i++) {
				if (!empty(trim($temp_improvement_status_array[$i]))) {
					array_push($improvement_status_array, trim($temp_improvement_status_array[$i]));
				}
			}
		} elseif (is_array($improvement_status)) {
			$temp_improvement_status_array = $improvement_status;
			$improvement_status_array = array();
			for ($i = 0; $i < count($temp_improvement_status_array); $i++) {
				if (!empty(trim($temp_improvement_status_array[$i]))) {
					array_push($improvement_status_array, trim($temp_improvement_status_array[$i]));
				}
			}
		}

		// Table names and datasets
		$table_names = self::getTableNames($organism, $dataset);
		$key_column = $table_names["key_column"];
		$gff_table = $table_names["gff_table"];
		$accession_mapping_table = $table_names["accession_mapping_table"];

		for ($i = 0; $i < count($gene_array); $i++) {

			// Generate SQL string
			$query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
			$query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
			$query_str = $query_str . "WHERE Name IN ('" . $gene_array[$i] . "');";

			$temp_gene_result_arr = DB::connection($db)->select($query_str);

			$query_str = self::getDataQueryString(
				$organism,
				$dataset,
				$db,
				$gff_table,
				$accession_mapping_table,
				$gene_array[$i],
				$temp_gene_result_arr[0]->Chromosome,
				""
			);

			$result_arr = DB::connection($db)->select($query_str);

			if (!isset($allele_catalog_result_arr)) {
				$allele_catalog_result_arr = (array) $result_arr;
			} else {
				$allele_catalog_result_arr = array_merge($allele_catalog_result_arr, (array) $result_arr);
			}
		}

		for ($i = 0; $i < count($allele_catalog_result_arr); $i++) {
			if (preg_match("/\+/i", $allele_catalog_result_arr[$i]->Imputation)) {
				$allele_catalog_result_arr[$i]->Imputation = "+";
			} else {
				$allele_catalog_result_arr[$i]->Imputation = "";
			}
		}

		return json_encode($allele_catalog_result_arr);
	}


	public function ViewAllByAccessionsAndGenePage(Request $request, $organism)
	{
		try {
			$organism = preg_replace('/\s+/', '', $organism);

			// Database
			$db = "KBC_" . $organism;

			$query_str = "SET SESSION group_concat_max_len = 1000000; ";
			$set_group_concat_max_len_result = DB::connection($db)->select($query_str);

			$dataset = $request->dataset_2;
			$gene = $request->gene_2;
			$accession = $request->accession_2;

			$dataset = self::clean_malicious_input($dataset);
			$dataset = preg_replace('/\s+/', '', $dataset);

			$gene = self::clean_malicious_input($gene);
			$gene = preg_replace('/\s+/', '', $gene);

			$accession = self::clean_malicious_input($accession);

			$accession_array = array();
			if (is_string($accession)) {
				$accession = trim($accession);
				$temp_accession_array = preg_split("/[;, \n]+/", $accession);
				$accession_array = array();
				for ($i = 0; $i < count($temp_accession_array); $i++) {
					if (!empty(trim($temp_accession_array[$i]))) {
						array_push($accession_array, trim($temp_accession_array[$i]));
					}
				}
			} elseif (is_array($accession)) {
				$temp_accession_array = $accession;
				$accession_array = array();
				for ($i = 0; $i < count($temp_accession_array); $i++) {
					if (!empty(trim($temp_accession_array[$i]))) {
						array_push($accession_array, trim($temp_accession_array[$i]));
					}
				}
			}

			// Table names and datasets
			$table_names = self::getTableNames($organism, $dataset);
			$key_column = $table_names["key_column"];
			$gff_table = $table_names["gff_table"];
			$accession_mapping_table = $table_names["accession_mapping_table"];

			try {
				// Generate SQL string
				$query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
				$query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
				$query_str = $query_str . "WHERE Name IN ('" . $gene . "');";

				$gene_result_arr = DB::connection($db)->select($query_str);

				if ($organism == "Zmays" && $dataset == "Maize1210") {
					// Generate SQL string
					$query_str = "WHERE (ACD.Accession IN ('";
					for ($i = 0; $i < count($accession_array); $i++) {
						if ($i < (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]) . "', '";
						} elseif ($i == (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]);
						}
					}
					$query_str = $query_str . "')) ";
					$query_str = $query_str . "OR (ACD.Panzea_Accession IN ('";
					for ($i = 0; $i < count($accession_array); $i++) {
						if ($i < (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]) . "', '";
						} elseif ($i == (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]);
						}
					}
					$query_str = $query_str . "')) ";

					$query_str = self::getDataQueryString(
						$organism,
						$dataset,
						$db,
						$gff_table,
						$accession_mapping_table,
						$gene,
						$gene_result_arr[0]->Chromosome,
						$query_str
					);
				} elseif ($organism == "Athaliana" && $dataset == "Arabidopsis1135") {
					// Generate SQL string
					$query_str = "WHERE (ACD.Accession IN ('";
					for ($i = 0; $i < count($accession_array); $i++) {
						if ($i < (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]) . "', '";
						} elseif ($i == (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]);
						}
					}
					$query_str = $query_str . "')) ";
					$query_str = $query_str . "OR (ACD.TAIR_Accession IN ('";
					for ($i = 0; $i < count($accession_array); $i++) {
						if ($i < (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]) . "', '";
						} elseif ($i == (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]);
						}
					}
					$query_str = $query_str . "')) ";
					$query_str = $query_str . "OR (ACD.Name IN ('";
					for ($i = 0; $i < count($accession_array); $i++) {
						if ($i < (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]) . "', '";
						} elseif ($i == (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]);
						}
					}
					$query_str = $query_str . "')) ";

					$query_str = self::getDataQueryString(
						$organism,
						$dataset,
						$db,
						$gff_table,
						$accession_mapping_table,
						$gene,
						$gene_result_arr[0]->Chromosome,
						$query_str
					);
				} elseif ($organism == "Ptrichocarpa" && $dataset == "PopulusTrichocarpa882") {
					// Generate SQL string
					$query_str = "WHERE (ACD.Accession IN ('";
					for ($i = 0; $i < count($accession_array); $i++) {
						if ($i < (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]) . "', '";
						} elseif ($i == (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]);
						}
					}
					$query_str = $query_str . "')) ";
					$query_str = $query_str . "OR (ACD.CBI_Coding_ID IN ('";
					for ($i = 0; $i < count($accession_array); $i++) {
						if ($i < (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]) . "', '";
						} elseif ($i == (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]);
						}
					}
					$query_str = $query_str . "')) ";

					$query_str = self::getDataQueryString(
						$organism,
						$dataset,
						$db,
						$gff_table,
						$accession_mapping_table,
						$gene,
						$gene_result_arr[0]->Chromosome,
						$query_str
					);
				} else {
					// Generate SQL string
					$query_str = "WHERE (ACD.Accession IN ('";
					for ($i = 0; $i < count($accession_array); $i++) {
						if ($i < (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]) . "', '";
						} elseif ($i == (count($accession_array) - 1)) {
							$query_str = $query_str . trim($accession_array[$i]);
						}
					}
					$query_str = $query_str . "')) ";

					$query_str = self::getDataQueryString(
						$organism,
						$dataset,
						$db,
						$gff_table,
						$accession_mapping_table,
						$gene,
						$gene_result_arr[0]->Chromosome,
						$query_str
					);
				}

				$result_arr = DB::connection($db)->select($query_str);
			} catch (\Throwable $e) {
				$result_arr = (object)array();
			}

			// Package variables that need to go to the view
			$info = [
				'organism' => $organism,
				'dataset' => $dataset,
				'gene' => $gene,
				'accession_array' => $accession_array,
				'result_arr' => $result_arr
			];

			// Return to view
			return view('system/tools/AlleleCatalogTool/viewAllByAccessionsAndGene')->with('info', $info);
		} catch (\Throwable $e) {
			abort(500);
		}
	}


	public function QueryAllByAccessionsAndGene(Request $request, $organism)
	{
		$organism = preg_replace('/\s+/', '', $organism);

		// Database
		$db = "KBC_" . $organism;

		$query_str = "SET SESSION group_concat_max_len = 1000000; ";
		$set_group_concat_max_len_result = DB::connection($db)->select($query_str);

		$dataset = $request->Dataset;
		$gene = $request->Gene;
		$accession = $request->Accession_Array;

		$dataset = self::clean_malicious_input($dataset);
		$dataset = preg_replace('/\s+/', '', $dataset);

		$gene = self::clean_malicious_input($gene);
		$gene = preg_replace('/\s+/', '', $gene);

		$accession = self::clean_malicious_input($accession);

		if (is_string($accession)) {
			$accession = trim($accession);
			$temp_accession_array = preg_split("/[;, \n]+/", $accession);
			$accession_array = array();
			for ($i = 0; $i < count($temp_accession_array); $i++) {
				if (!empty(trim($temp_accession_array[$i]))) {
					array_push($accession_array, trim($temp_accession_array[$i]));
				}
			}
		} elseif (is_array($accession)) {
			$temp_accession_array = $accession;
			$accession_array = array();
			for ($i = 0; $i < count($temp_accession_array); $i++) {
				if (!empty(trim($temp_accession_array[$i]))) {
					array_push($accession_array, trim($temp_accession_array[$i]));
				}
			}
		}

		// Table names and datasets
		$table_names = self::getTableNames($organism, $dataset);
		$key_column = $table_names["key_column"];
		$gff_table = $table_names["gff_table"];
		$accession_mapping_table = $table_names["accession_mapping_table"];

		// Generate SQL string
		$query_str = "SELECT Chromosome, Start, End, Name AS Gene ";
		$query_str = $query_str . "FROM " . $db . "." . $gff_table . " ";
		$query_str = $query_str . "WHERE Name IN ('" . $gene . "');";

		$gene_result_arr = DB::connection($db)->select($query_str);


		if ($organism == "Zmays" && $dataset == "Maize1210") {
			// Generate SQL string
			$query_str = "WHERE (ACD.Accession IN ('";
			for ($i = 0; $i < count($accession_array); $i++) {
				if ($i < (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]) . "', '";
				} elseif ($i == (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]);
				}
			}
			$query_str = $query_str . "')) ";
			$query_str = $query_str . "OR (ACD.Panzea_Accession IN ('";
			for ($i = 0; $i < count($accession_array); $i++) {
				if ($i < (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]) . "', '";
				} elseif ($i == (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]);
				}
			}
			$query_str = $query_str . "')) ";

			$query_str = self::getDataQueryString(
				$organism,
				$dataset,
				$db,
				$gff_table,
				$accession_mapping_table,
				$gene,
				$gene_result_arr[0]->Chromosome,
				$query_str
			);
		} elseif ($organism == "Athaliana" && $dataset == "Arabidopsis1135") {
			// Generate SQL string
			$query_str = "WHERE (ACD.Accession IN ('";
			for ($i = 0; $i < count($accession_array); $i++) {
				if ($i < (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]) . "', '";
				} elseif ($i == (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]);
				}
			}
			$query_str = $query_str . "')) ";
			$query_str = $query_str . "OR (ACD.TAIR_Accession IN ('";
			for ($i = 0; $i < count($accession_array); $i++) {
				if ($i < (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]) . "', '";
				} elseif ($i == (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]);
				}
			}
			$query_str = $query_str . "')) ";
			$query_str = $query_str . "OR (ACD.Name IN ('";
			for ($i = 0; $i < count($accession_array); $i++) {
				if ($i < (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]) . "', '";
				} elseif ($i == (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]);
				}
			}
			$query_str = $query_str . "')) ";

			$query_str = self::getDataQueryString(
				$organism,
				$dataset,
				$db,
				$gff_table,
				$accession_mapping_table,
				$gene,
				$gene_result_arr[0]->Chromosome,
				$query_str
			);
		} elseif ($organism == "Osativa" && $dataset == "Rice166") {
			// Generate SQL string
			$query_str = "WHERE (ACD.Accession IN ('";
			for ($i = 0; $i < count($accession_array); $i++) {
				if ($i < (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]) . "', '";
				} elseif ($i == (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]);
				}
			}
			$query_str = $query_str . "')) ";

			$query_str = self::getDataQueryString(
				$organism,
				$dataset,
				$db,
				$gff_table,
				$accession_mapping_table,
				$gene,
				$gene_result_arr[0]->Chromosome,
				$query_str
			);
		} elseif ($organism == "Osativa" && $dataset == "Rice3000") {
			// Generate SQL string
			$query_str = "WHERE (ACD.Accession IN ('";
			for ($i = 0; $i < count($accession_array); $i++) {
				if ($i < (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]) . "', '";
				} elseif ($i == (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]);
				}
			}
			$query_str = $query_str . "')) ";

			$query_str = self::getDataQueryString(
				$organism,
				$dataset,
				$db,
				$gff_table,
				$accession_mapping_table,
				$gene,
				$gene_result_arr[0]->Chromosome,
				$query_str
			);
		} elseif ($organism == "Ptrichocarpa" && $dataset == "PopulusTrichocarpa882") {
			// Generate SQL string
			$query_str = "WHERE (ACD.Accession IN ('";
			for ($i = 0; $i < count($accession_array); $i++) {
				if ($i < (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]) . "', '";
				} elseif ($i == (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]);
				}
			}
			$query_str = $query_str . "')) ";
			$query_str = $query_str . "OR (ACD.CBI_Coding_ID IN ('";
			for ($i = 0; $i < count($accession_array); $i++) {
				if ($i < (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]) . "', '";
				} elseif ($i == (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]);
				}
			}
			$query_str = $query_str . "')) ";

			$query_str = self::getDataQueryString(
				$organism,
				$dataset,
				$db,
				$gff_table,
				$accession_mapping_table,
				$gene,
				$gene_result_arr[0]->Chromosome,
				$query_str
			);
		} else {
			// Generate SQL string
			$query_str = "WHERE (ACD.Accession IN ('";
			for ($i = 0; $i < count($accession_array); $i++) {
				if ($i < (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]) . "', '";
				} elseif ($i == (count($accession_array) - 1)) {
					$query_str = $query_str . trim($accession_array[$i]);
				}
			}
			$query_str = $query_str . "')) ";

			$query_str = self::getDataQueryString(
				$organism,
				$dataset,
				$db,
				$gff_table,
				$accession_mapping_table,
				$gene,
				$gene_result_arr[0]->Chromosome,
				$query_str
			);
		}

		$result_arr = DB::connection($db)->select($query_str);

		for ($i = 0; $i < count($result_arr); $i++) {
			if (preg_match("/\+/i", $result_arr[$i]->Imputation)) {
				$result_arr[$i]->Imputation = "+";
			} else {
				$result_arr[$i]->Imputation = "";
			}
		}

		return json_encode($result_arr);
	}


	public function ViewVariantAndPhenotypePage(Request $request, $organism)
	{
		try {
			$organism = preg_replace('/\s+/', '', $organism);

			// Database
			$db = "KBC_" . $organism;

			$chromosome = $request->Chromosome;
			$position = $request->Position;
			$gene = $request->Gene;
			$dataset = $request->Dataset;

			$chromosome = self::clean_malicious_input($chromosome);
			$position = self::clean_malicious_input($position);
			$gene = self::clean_malicious_input($gene);
			$dataset = self::clean_malicious_input($dataset);

			// Trim string
			if (is_string($chromosome)) {
				$chromosome = preg_replace('/\s+/', '', $chromosome);
			}
			if (is_string($position)) {
				$position = preg_replace("/[^0-9.]/", "", $position);
			}
			if (is_string($gene)) {
				$gene = preg_replace('/\s+/', '', $gene);
			}
			if (is_string($dataset)) {
				$dataset = preg_replace('/\s+/', '', $dataset);
			}

			// Table names and datasets
			$table_names = self::getTableNames($organism, $dataset);
			$key_column = $table_names["key_column"];
			$gff_table = $table_names["gff_table"];
			$accession_mapping_table = $table_names["accession_mapping_table"];
			$phenotype_table = $table_names["phenotype_table"];
			$phenotype_selection_table = $table_names["phenotype_selection_table"];
			$genotype_table = "act_" . $dataset . "_genotype_" . $chromosome;

			// Query string
			$query_str = "SELECT * FROM " . $db . "." . $phenotype_selection_table . ";";

			try {
				$phenotype_selection_arr = DB::connection($db)->select($query_str);
			} catch (\Throwable $e) {
				$phenotype_selection_arr = array();
			}

			$query_str = "
			SELECT DISTINCT Genotype
			FROM " . $db . "." . $genotype_table . "
			WHERE ((Chromosome = '" . $chromosome . "')
			AND (Position = " . $position . "))
			ORDER BY Genotype;
		";

			$genotype_selection_arr = DB::connection($db)->select($query_str);

			// Package variables that need to go to the view
			$info = [
				'organism' => $organism,
				'chromosome' => $chromosome,
				'position' => $position,
				'gene' => $gene,
				'dataset' => $dataset,
				'phenotype_selection_arr' => $phenotype_selection_arr,
				'genotype_selection_arr' => $genotype_selection_arr
			];

			// Return to view
			return view('system/tools/AlleleCatalogTool/viewVariantAndPhenotype')->with('info', $info);
		} catch (\Throwable $e) {
			abort(500);
		}
	}


	public function QueryPhenotypeDescription(Request $request, $organism)
	{
		$organism = preg_replace('/\s+/', '', $organism);

		// Database
		$db = "KBC_" . $organism;

		$dataset = $request->Dataset;

		$dataset = self::clean_malicious_input($dataset);
		$dataset = preg_replace('/\s+/', '', $dataset);

		// Table names and datasets
		$table_names = self::getTableNames($organism, $dataset);
		$phenotype_selection_table = $table_names["phenotype_selection_table"];

		// Query string
		$query_str = "SELECT Phenotype, Phenotype_Description FROM " . $db . "." . $phenotype_selection_table . ";";

		$result_arr = DB::connection($db)->select($query_str);

		return json_encode($result_arr);
	}


	public function QueryVariantAndPhenotype(Request $request, $organism)
	{
		$organism = preg_replace('/\s+/', '', $organism);

		// Database
		$db = "KBC_" . $organism;

		$chromosome = $request->Chromosome;
		$position = $request->Position;
		$gene = $request->Gene;
		$genotype = $request->Genotype;
		$phenotype = $request->Phenotype;
		$dataset = $request->Dataset;

		$chromosome = self::clean_malicious_input($chromosome);
		$chromosome = preg_replace('/\s+/', '', $chromosome);

		$position = self::clean_malicious_input($position);
		$position = preg_replace("/[^0-9.]/", "", $position);

		$gene = self::clean_malicious_input($gene);
		$gene = preg_replace('/\s+/', '', $gene);

		$genotype = self::clean_malicious_input($genotype);

		$phenotype = self::clean_malicious_input($phenotype);

		$dataset = self::clean_malicious_input($dataset);
		$dataset = preg_replace('/\s+/', '', $dataset);

		if (is_string($genotype)) {
			$genotype = trim($genotype);
			$temp_genotype_array = preg_split("/[;, \n]+/", $genotype);
			$genotype_array = array();
			for ($i = 0; $i < count($temp_genotype_array); $i++) {
				if (!empty(trim($temp_genotype_array[$i]))) {
					array_push($genotype_array, trim($temp_genotype_array[$i]));
				}
			}
		} elseif (is_array($genotype)) {
			$temp_genotype_array = $genotype;
			$genotype_array = array();
			for ($i = 0; $i < count($temp_genotype_array); $i++) {
				if (!empty(trim($temp_genotype_array[$i]))) {
					array_push($genotype_array, trim($temp_genotype_array[$i]));
				}
			}
		}

		if (is_string($phenotype)) {
			$phenotype = trim($phenotype);
			$temp_phenotype_array = preg_split("/[;, \n]+/", $phenotype);
			$phenotype_array = array();
			for ($i = 0; $i < count($temp_phenotype_array); $i++) {
				if (!empty(trim($temp_phenotype_array[$i]))) {
					array_push($phenotype_array, trim($temp_phenotype_array[$i]));
				}
			}
		} elseif (is_array($phenotype)) {
			$temp_phenotype_array = $phenotype;
			$phenotype_array = array();
			for ($i = 0; $i < count($temp_phenotype_array); $i++) {
				if (!empty(trim($temp_phenotype_array[$i]))) {
					array_push($phenotype_array, trim($temp_phenotype_array[$i]));
				}
			}
		}

		// Table names and datasets
		$table_names = self::getTableNames($organism, $dataset);
		$key_column = $table_names["key_column"];
		$gff_table = $table_names["gff_table"];
		$accession_mapping_table = $table_names["accession_mapping_table"];
		$phenotype_table = $table_names["phenotype_table"];
		$phenotype_selection_table = $table_names["phenotype_selection_table"];
		$genotype_table = "act_" . $dataset . "_genotype_" . $chromosome;
		$functional_effect_table = "act_" . $dataset . "_func_eff_" . $chromosome;

		// Construct query string
		$query_str = "SELECT GENO.Chromosome, GENO.Position, GENO.Accession, ";
		if ($organism == "Osativa") {
			$query_str = $query_str . "AM.Accession_Name, AM.IRIS_ID, AM.Subpopulation, ";
		} elseif ($organism == "Athaliana") {
			$query_str = $query_str . "AM.TAIR_Accession, AM.Name, AM.Admixture_Group, ";
		} elseif ($organism == "Zmays") {
			$query_str = $query_str . "AM.Improvement_Status, ";
		} elseif ($organism == "Ptrichocarpa") {
			$query_str = $query_str . "AM.CBI_Coding_ID, ";
		}
		$query_str = $query_str . "GENO.Genotype, ";
		$query_str = $query_str . "COALESCE( FUNC.Functional_Effect, GENO.Category ) AS Functional_Effect, GENO.Imputation ";
		if (isset($phenotype_array) && is_array($phenotype_array) && !empty($phenotype_array)) {
			for ($i = 0; $i < count($phenotype_array); $i++) {
				$query_str = $query_str . ", PH." . $phenotype_array[$i] . " ";
			}
		}
		$query_str = $query_str . "FROM ( ";
		$query_str = $query_str . "	SELECT G.Chromosome, G.Position, G.Accession, G.Genotype, G.Category, G.Imputation ";
		$query_str = $query_str . "	FROM " . $db . "." . $genotype_table . " AS G ";
		$query_str = $query_str . "	WHERE (G.Chromosome = '" . $chromosome . "') ";
		$query_str = $query_str . "	AND (G.Position = " . $position . ") ";
		if (count($genotype_array) > 0) {
			$query_str = $query_str . "	AND (G.Genotype IN ('";
			for ($i = 0; $i < count($genotype_array); $i++) {
				if ($i < (count($genotype_array) - 1)) {
					$query_str = $query_str . trim($genotype_array[$i]) . "', '";
				} elseif ($i == (count($genotype_array) - 1)) {
					$query_str = $query_str . trim($genotype_array[$i]);
				}
			}
			$query_str = $query_str . "')) ";
		}
		$query_str = $query_str . ") AS GENO ";
		$query_str = $query_str . "LEFT JOIN ( ";
		$query_str = $query_str . "	SELECT F.Chromosome, F.Position, F.Allele, F.Functional_Effect, F.Gene, F.Amino_Acid_Change ";
		$query_str = $query_str . "	FROM " . $db . "." . $functional_effect_table . " AS F ";
		$query_str = $query_str . "	WHERE (F.Chromosome = '" . $chromosome . "') ";
		$query_str = $query_str . "	AND (F.Position = " . $position . ") ";
		if (count($genotype_array) > 0) {
			$query_str = $query_str . "	AND (F.Allele IN ('";
			for ($i = 0; $i < count($genotype_array); $i++) {
				if ($i < (count($genotype_array) - 1)) {
					$query_str = $query_str . trim($genotype_array[$i]) . "', '";
				} elseif ($i == (count($genotype_array) - 1)) {
					$query_str = $query_str . trim($genotype_array[$i]);
				}
			}
			$query_str = $query_str . "')) ";
		}
		$query_str = $query_str . "AND F.Gene LIKE '%" . $gene . "%' ";
		$query_str = $query_str . ") AS FUNC ";
		$query_str = $query_str . "ON GENO.Chromosome = FUNC.Chromosome AND GENO.Position = FUNC.Position AND GENO.Genotype = FUNC.Allele ";
		$query_str = $query_str . "LEFT JOIN " . $db . "." . $accession_mapping_table . " AS AM ";
		$query_str = $query_str . "ON CAST(GENO.Accession AS BINARY) = CAST(AM.Accession AS BINARY) ";
		if (isset($phenotype_array) && is_array($phenotype_array) && !empty($phenotype_array)) {
			$query_str = $query_str . "LEFT JOIN " . $db . "." . $phenotype_table . " AS PH ";
			if ($organism == "Athaliana" && $dataset == "Arabidopsis1135") {
				$query_str = $query_str . "ON CAST(AM.TAIR_Accession AS BINARY) = CAST(PH.Accession AS BINARY) ";
			} else {
				$query_str = $query_str . "ON CAST(GENO.Accession AS BINARY) = CAST(PH.Accession AS BINARY) ";
			}
		}
		$query_str = $query_str . "ORDER BY GENO.Chromosome, GENO.Position, GENO.Genotype; ";

		$result_arr = DB::connection($db)->select($query_str);

		return json_encode($result_arr);
	}


	public function ViewVariantAndPhenotypeFiguresPage(Request $request, $organism)
	{
		try {
			$organism = preg_replace('/\s+/', '', $organism);

			// Database
			$db = "KBC_" . $organism;

			$chromosome = $request->chromosome_1;
			$position = $request->position_1;
			$gene = $request->gene_1;
			$genotype = $request->genotype_1;
			$phenotype = $request->phenotype_1;
			$dataset = $request->dataset_1;

			$chromosome = self::clean_malicious_input($chromosome);
			$chromosome = preg_replace('/\s+/', '', $chromosome);

			$position = self::clean_malicious_input($position);
			$position = preg_replace("/[^0-9.]/", "", $position);

			$gene = self::clean_malicious_input($gene);
			$gene = preg_replace('/\s+/', '', $gene);

			$genotype = self::clean_malicious_input($genotype);

			$phenotype = self::clean_malicious_input($phenotype);

			$dataset = self::clean_malicious_input($dataset);
			$dataset = preg_replace('/\s+/', '', $dataset);

			if (is_string($genotype)) {
				$genotype = trim($genotype);
				$temp_genotype_array = preg_split("/[;, \n]+/", $genotype);
				$genotype_array = array();
				for ($i = 0; $i < count($temp_genotype_array); $i++) {
					if (!empty(trim($temp_genotype_array[$i]))) {
						array_push($genotype_array, trim($temp_genotype_array[$i]));
					}
				}
			} elseif (is_array($genotype)) {
				$temp_genotype_array = $genotype;
				$genotype_array = array();
				for ($i = 0; $i < count($temp_genotype_array); $i++) {
					if (!empty(trim($temp_genotype_array[$i]))) {
						array_push($genotype_array, trim($temp_genotype_array[$i]));
					}
				}
			}

			// Package variables that need to go to the view
			$info = [
				'organism' => $organism,
				'chromosome' => $chromosome,
				'position' => $position,
				'gene' => $gene,
				'genotype_array' => $genotype_array,
				'phenotype' => $phenotype,
				'dataset' => $dataset
			];

			// Return to view
			return view('system/tools/AlleleCatalogTool/viewVariantAndPhenotypeFigures')->with('info', $info);
		} catch (\Throwable $e) {
			abort(500);
		}
	}
}
