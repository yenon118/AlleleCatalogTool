// Allele Catalog Tool
Route::get('/system/tools/AlleleCatalogTool/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@AlleleCatalogToolPage')->name('system.tools.AlleleCatalogTool');
Route::get('/system/tools/AlleleCatalogTool/viewAllByGenes/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@ViewAllByGenesPage')->name('system.tools.AlleleCatalogTool.viewAllByGenes');
Route::get('/system/tools/AlleleCatalogTool/viewAllByAccessionAndGene/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@ViewAllByAccessionAndGenePage')->name('system.tools.AlleleCatalogTool.viewAllByAccessionAndGene');
Route::get('/system/tools/AlleleCatalogTool/viewAllByGenes/getAccessionsByImpStatusGenePositionGenotypeDesc/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@GetAccessionsByImpStatusGenePositionGenotypeDesc')->name('system.tools.AlleleCatalogTool.viewAllByGenes.getAccessionsByImpStatusGenePositionGenotypeDesc');
Route::get('/system/tools/AlleleCatalogTool/viewAllByGenes/getImputationData/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@getImputationData')->name('system.tools.AlleleCatalogTool.viewAllByGenes.getImputationData');
Route::get('/system/tools/AlleleCatalogTool/viewAllByGenes/downloadAllCountsByGene/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@DownloadAllCountsByGene')->name('system.tools.AlleleCatalogTool.viewAllByGenes.downloadAllCountsByGene');
Route::get('/system/tools/AlleleCatalogTool/viewAllByGenes/downloadAllByGene/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@DownloadAllByGene')->name('system.tools.AlleleCatalogTool.viewAllByGenes.downloadAllByGene');
Route::get('/system/tools/AlleleCatalogTool/viewAllByGenes/downloadAllCountsByMultipleGenes/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@DownloadAllCountsByMultipleGenes')->name('system.tools.AlleleCatalogTool.viewAllByGenes.downloadAllCountsByMultipleGenes');
Route::get('/system/tools/AlleleCatalogTool/viewAllByGenes/downloadAllByMultipleGenes/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@DownloadAllByMultipleGenes')->name('system.tools.AlleleCatalogTool.viewAllByGenes.downloadAllByMultipleGenes');
Route::get('/system/tools/AlleleCatalogTool/viewAllByAccessionAndGene/downloadAllByAccessionsAndGene/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@DownloadAllByAccessionsAndGene')->name('system.tools.AlleleCatalogTool.viewAllByAccessionAndGene.downloadAllByAccessionsAndGene');
