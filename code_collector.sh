
cp -rf /data/html/Prod/KBCommons_multi/resources/views/system/tools/AlleleCatalogTool /home/chanye/projects/

mkdir -p /home/chanye/projects/AlleleCatalogTool/controller
mkdir -p /home/chanye/projects/AlleleCatalogTool/routes

cp -rf /data/html/Prod/KBCommons_multi/app/Http/Controllers/System/Tools/KBCToolsAlleleCatalogToolController.php /home/chanye/projects/AlleleCatalogTool/controller/

cp -rf /data/html/Prod/KBCommons_multi/public/system/home/AlleleCatalogTool/js /home/chanye/projects/AlleleCatalogTool/
cp -rf /data/html/Prod/KBCommons_multi/public/system/home/AlleleCatalogTool/css /home/chanye/projects/AlleleCatalogTool/

grep "AlleleCatalogTool" /data/html/Prod/KBCommons_multi/routes/web.php | grep -v -e "AlleleCatalogTool2" -e "^//" > /home/chanye/projects/AlleleCatalogTool/routes/web.php
