{config_load file="member.ini" section="ucenter"}
{config_load file="common.ini"}
<!DOCTYPE html>
<html lang="en">

    <head>

        {include file="common/header.tpl"}
        <meta name="description" content="">
        <meta name="author" content="">

        <title>{#ControlPanel#}-{#CompanyName#}</title>


        {include file="common/header.tpl"}
        <!-- MetisMenu CSS -->
        <link href="static/css/metisMenu.min.css" rel="stylesheet">

        <!-- Timeline CSS -->
        <!--<link href="static/css/timeline.css" rel="stylesheet">-->

        <!-- Custom CSS -->
        <link href="static/css/sb-admin-2.css" rel="stylesheet">

        <!-- Morris Charts CSS -->
        <link href="static/css/morris.css" rel="stylesheet">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body>

        <div id="wrapper">

            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php">{#ControlPanel#}</a>
                </div>
                <!-- /.navbar-header -->

                {include file="member/nav-header.tpl"}
                {include file="member/nav-side.tpl"}
                <!-- /.navbar-top-links -->
            </nav>

            <div id="page-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">{#Dashboard#}</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- Information for Demostration -->
                <div class="alert alert-info alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>Notice </strong> The graph and data in the page is just for demostration, not in the scope of the project.
                </div>
                <div class ="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-heartbeat fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge" id="hbdata">75</div>
                                        <div>{#HBAverage#}</div>
                                    </div>
                                </div>
                            </div>
                            <a href="#">
                                <div class="panel-footer">
                                    <span class="pull-left">{#Details#}</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="panel panel-green">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-male fa-5x"></i>
                                    </div>
                                    <div class="col-xs-9 text-right">
                                        <div class="huge" id="stepdata">1000</div>
                                        <div>{#Steps#}</div>
                                    </div>
                                </div>
                            </div>
                            <a href="#">
                                <div class="panel-footer">
                                    <span class="pull-left">{#Details#}</span>
                                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                    <div class="clearfix"></div>
                                </div>
                            </a>
                        </div>
                    </div>
                    <!-- /.row -->

                    <!-- /.row -->

                    <!-- /.row -->
                </div>
                <div class="row">
                    <div class="col-lg-8 col-md-7">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-heartbeat fa-fw"></i> {#HBChart#}
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <div id="morris-heartbeat"></div>
                            </div>
                            <!-- /.panel-body -->
                        </div>
                        <!-- /.panel -->
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-heart fa-fw"></i> {#BPChart#}
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <div id="morris-bp"></div>
                            </div>
                            <!-- /.panel-body -->
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <i class="fa fa-microphone fa-fw"></i> {#VoiceCmd#}
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body" id="panel-vcinput">
                                <div class="form-group input-group">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="vc-input-btn"><i class="fa fa-microphone"></i></button>
                                    </span>
                                    <input type="text" id="vc-input-cmd" class="form-control" x-webkit-speech >
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button" id="vc-input-go">Go</button>
                                    </span>
                                </div>
                            </div>
                            <!-- /.panel-body -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- /#page-wrapper -->

        </div>
        <!-- /#wrapper -->


        {include file ="common/footer.tpl"}

        <!-- Metis Menu Plugin JavaScript -->
        <script src="static/js/metisMenu.min.js"></script>

        <!-- Morris Charts JavaScript -->
        <script src="static/js/raphael-min.js"></script>
        <script src="static/js/morris.min.js"></script>
        
        <script src="static/js/data-demo.js"></script>

        <!-- Custom Theme JavaScript -->
        <script src="static/js/scripts-control.js"></script>
        <script src="static/js/scripts-voice.js"></script>

    </body>

</html>
