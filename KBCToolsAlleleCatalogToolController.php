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
    function __construct() {
        $this->db_kbc_wrapper = new DBKBCWrapperClass;
    }

    public function AlleleCatalogToolPage(Request $request, $organism) {
        $admin_db_wapper = new DBAdminWrapperClass;

        $db = "KBC_" . $organism;

        if ($organism == "Zmays") {
            $table_name = "Zmays_Panzea_AGPv3_Allele_Catalog";
            $dataset_array = array("Zmays_Panzea_AGPv3_Allele_Catalog");
        } elseif ($organism == "Athaliana") {
            $table_name = "Athaliana_Arabidopsis_TAIR10_Allele_Catalog";
            $dataset_array = array("Athaliana_Arabidopsis_TAIR10_Allele_Catalog");
        }

        $sql = "SELECT DISTINCT Gene FROM " . $table_name . " WHERE Gene IS NOT NULL LIMIT 3;";
        $gene_array = DB::connection($db)->select($sql);

        $sql = "SELECT DISTINCT Accession FROM " . $table_name . " WHERE Accession IS NOT NULL LIMIT 3;";
        $accession_array = DB::connection($db)->select($sql);

        $info = [
            'organism' => $organism,
            'dataset_array' => $dataset_array,
            'gene_array' => $gene_array,
            'accession_array' => $accession_array,
        ];

        return view('system/tools/AlleleCatalogTool/AlleleCatalogTool')->with('info', $info);
    }


    public function ViewAllByGenesPage(Request $request, $organism) {
        $admin_db_wapper = new DBAdminWrapperClass;

        $dataset1 = $request->dataset1;
        $gene1 = $request->gene1;

        $info = [
            'organism' => $organism,
            'dataset1' => $dataset1,
            'gene1' => $gene1
        ];

        return view('system/tools/AlleleCatalogTool/viewAllByGenes')->with('info', $info);
    }


    public function ViewAllByAccessionAndGenePage(Request $request, $organism) {
        $admin_db_wapper = new DBAdminWrapperClass;

        $dataset2 = $request->dataset2;
        $accession = $request->accession;
        $gene2 = $request->gene2;

        $info = [
            'organism' => $organism,
            'dataset2' => $dataset2,
            'accession' => $accession,
            'gene2' => $gene2
        ];

        return view('system/tools/AlleleCatalogTool/viewAllByAccessionAndGene')->with('info', $info);
    }
}
