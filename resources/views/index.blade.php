<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Outlet for SPA</title>

        <link rel="stylesheet" type="text/css" href="css/app.css">
        <link rel="stylesheet" href="css/spin.css" />
        <?php
            
        ?>
        <script>
            window.trans = <?php
                // get name folder
                $directories = File::directories(resource_path() . '/lang/');
                $trans = [];
                foreach ($directories as $folder) {
                    $nameFolder = explode('/', $folder);
                    $lang = end($nameFolder);
                    // copy all translations from /resources/lang/CURRENT_LOCALE/* to global JS variable
                    $lang_files = File::files(resource_path() . '/lang/' . end($nameFolder));
                    $trans[$lang] = [];
                    foreach ($lang_files as $f) {
                        $filename = pathinfo($f)['filename'];
                        $trans[$lang][$filename] = trans($filename);
                    }
                }
                echo json_encode($trans);
            ?>;
            window.lang = "<?php echo App::getLocale() ?>";
        </script>
        
    </head>
    <body>
        <div id="loader">
            <div class="bg-loader"></div>
            <div class="loader-spin">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
            <div class="bar4"></div>
            <div class="bar5"></div>
            <div class="bar6"></div>
            </div>
        </div>
        <div id="root">
            <router-view></router-view>
        </div>
        <script src="js/googleChart.js"></script>
        <script type="text/javascript" src="js/index.js"></script>
        <script src="js/spin.js"></script>
    </body>
</html>