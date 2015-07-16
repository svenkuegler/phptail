<?php require './phptail.class.php'; $phptail=new phpTail(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon.png">

    <title>phpTail</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/default.css" rel="stylesheet">

    <!--[if IE 7]>
    <link rel="stylesheet" href="css/font-awesome-ie7.min.css">
    <![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
</head>

<body>

    <!-- Wrap all page content here -->
    <div id="wrap">

        <!-- Fixed navbar -->
        <div class="navbar navbar-default navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#home"><i class="fa fa-eye"></i> phpTail</a>
                </div>
                <div class="">
                    <ul class="nav navbar-nav">
                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#settingsModal"><?php echo _('Settings');?></a>
                        </li>
                        <?php if(count($phptail->getLogfiles())>0): ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo _('Logfiles');?> <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <?php foreach($phptail->getLogfiles() as $file):?>
                                <li>
                                    <a href="#a<?php echo $file->pos ?>">
                                        <?php echo $file->name ?></a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                    </ul>
                    <p class="navbar-text pull-right">
                        <?php echo $phptail->getVersionInfo(); ?></</p>
                </div>
                <!--/.nav-collapse -->
            </div>
        </div>

        <a name="home"></a>
        <div class="container">

            <noscript>
                <div class="alert alert-danger"><i class="icon-warning-sign"></i>
                    <?php echo _( "Please enable JavaScript in your Browser!"); ?>
                </div>
            </noscript>

            <!-- Messages -->
            <?php if(count($phptail->getMessages()>0)): foreach($phptail->getMessages() as $message):?>
            <div class="alert <?php echo $message["type"]?>">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <?php echo $message["text"]?>
            </div>
            <?php endforeach; endif; ?>

            <!-- Content -->
            <?php foreach($phptail->getLogfiles() as $file):?>
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-12">
                    <a name="a<?php echo $file->pos ?>"></a>
                    <label for="log<?php echo $file->pos?>"><span class="lead"><?php echo $file->file ?></span> </label>
                    <button class="btn pull-right btn-space-left" id="btnPause<?php echo $file->pos?>" onclick="refreshPause('btn<?php echo $file->pos?>', 'btnPause<?php echo $file->pos?>')"><i class="fa fa-pause"></i>
                    </button>
                    <button class="btn pull-right btn-space-left" id="btnFilter<?php echo $file->pos?>" onclick="setFilter('<?php echo $file->pos?>', '<?php echo $file->file?>', 'btnFilter<?php echo $file->pos?>')"><i class="fa fa-filter"></i>
                    </button>
                    <button class="btn pull-right btn-space-left autorefresh" id="btn<?php echo $file->pos?>" onclick="callRpc('btn<?php echo $file->pos?>', '<?php echo $file->pos?>', 'log<?php echo $file->pos?>')"><i class="fa fa-refresh"></i>
                    </button>
                    <div class="form-control logfile" id="log<?php echo $file->pos?>" style="height:<?php echo round(intval($file->lines / 2)) ?>em"></div>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </div>

    <div id="footer">
        <div class="container">
            <p class="text-muted credit">proudly presented by <a href="http://www.phptail.org">phpTail</a>
                <?php echo date( 'Y');?>
            </p>
        </div>
    </div>

    <!-- Settings Modal -->
    <div class="modal fade" id="settingsModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Settings</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal span12">
                        <fieldset>
                            <!-- Text input-->
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="maxsizetoload">Max Size to Load</label>
                                <div class="col-md-8">
                                    <input id="maxsizetoload" name="maxsizetoload" type="text" placeholder="1024000" class="form-control input-md" required="">
                                    <span class="help-block">The Max Filelen to load</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label" for="autoreload">Autoreload</label>
                                <div class="col-md-4">
                                    <div class="checkbox">
                                        <label for="autorefresh-0">
                                            <input type="checkbox" name="autoreload" id="autorefresh-0" value="1"> Dis/Enable Autoreload
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Text input-->
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="reloadinterval">Reload Interval</label>
                                <div class="col-md-8">
                                    <input id="reloadinterval" name="reloadinterval" type="text" placeholder="3000" class="form-control input-md" required="">
                                    <span class="help-block">Interval in Miliseconds</span>
                                </div>
                            </div>

                            <!-- Select Multiple -->
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="logfiles">Logfiles</label>
                                <div class="col-md-8">
                                    <select id="logfiles" name="logfiles" class="form-control" multiple="multiple">
                                        <option value="1">Logfile 1</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Grid -->
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="grid">Grid</label>
                                <div class="col-md-8">
                                    <div class="btn-group" data-toggle="buttons">
                                        <label class="btn btn-info active">
                                            <input type="radio" name="grid" id="grid1" value="1" autocomplete="off" checked><i class="fa fa-align-justify"></i> Default Grid
                                        </label>
                                        <label class="btn btn-info">
                                            <input type="radio" name="grid" id="grid2" value="2" autocomplete="off"><i class="fa fa-th-large"></i> 2 Cols
                                        </label>
                                    </div>
                                    <span class="help-block">Please choose a Grid.</span>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Set Filter for ....</h4>
                </div>
                <div class="modal-body">
                    <form class="form-inline">
                        <div class="form-group">
                            <label for="filterValue">Filter</label>
                            <input type="text" class="form-control" id="filterValue" placeholder="Filter">
                            <input type="hidden" id="filterFile" value="" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="writeFilter()">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery-1.10.2.min.js"></script>
    <script src="js/phptail.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <script type="text/javascript">
        clickAllButtons();
        $(document).ready(function () {
            setInterval("clickAllButtons()", <?php echo $phptail->getReloadInterval(); ?> );
        });
    </script>
</body>

</html>