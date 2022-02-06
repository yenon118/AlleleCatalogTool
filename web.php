// Allele Catalog Tool
Route::get('/system/tools/AlleleCatalogTool/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@AlleleCatalogToolPage')->name('system.tools.AlleleCatalogTool');
Route::get('/system/tools/AlleleCatalogTool/viewAllByGenes/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@ViewAllByGenesPage')->name('system.tools.AlleleCatalogTool.viewAllByGenes');
Route::get('/system/tools/AlleleCatalogTool/viewAllByAccessionAndGene/{organism}', 'System\Tools\KBCToolsAlleleCatalogToolController@ViewAllByAccessionAndGenePage')->name('system.tools.AlleleCatalogTool.viewAllByAccessionAndGene');

