
cp -rf /data/sites/KBCommons/resources/views/system/tools/AlleleCatalogTool /home/chanye/projects/

mkdir -p /home/chanye/projects/AlleleCatalogTool/controller
mkdir -p /home/chanye/projects/AlleleCatalogTool/routes

cp -rf /data/sites/KBCommons/app/Http/Controllers/System/Tools/KBCToolsAlleleCatalogToolController.php /home/chanye/projects/AlleleCatalogTool/controller/

cp -rf /data/sites/KBCommons/public/system/home/AlleleCatalogTool/* /home/chanye/projects/AlleleCatalogTool/

grep "AlleleCatalogTool" /data/sites/KBCommons/routes/web.php | grep -v -e "AlleleCatalogTool2" -e "^//" > /home/chanye/projects/AlleleCatalogTool/routes/web.php
