cp /data/html/Prod/KBCommons_multi/app/Http/Controllers/System/Tools/KBCToolsAlleleCatalogToolController.php /data/html/Prod/KBCommons_multi/resources/views/system/tools/AlleleCatalogTool/controller/

cp -r /data/html/Prod/KBCommons_multi/public/system/home/AlleleCatalogTool/css /data/html/Prod/KBCommons_multi/resources/views/system/tools/AlleleCatalogTool/

cp -r /data/html/Prod/KBCommons_multi/public/system/home/AlleleCatalogTool/js /data/html/Prod/KBCommons_multi/resources/views/system/tools/AlleleCatalogTool/

grep -e "Allele Catalog" -e "AlleleCatalog" /data/html/Prod/KBCommons_multi/routes/web.php > /data/html/Prod/KBCommons_multi/resources/views/system/tools/AlleleCatalogTool/routes/web.php

cp -r /data/html/Prod/KBCommons_multi/resources/views/system/tools/AlleleCatalogTool /home/chanye/projects/
