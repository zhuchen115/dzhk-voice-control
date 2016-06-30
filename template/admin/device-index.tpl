{config_load file="admin.ini" section="device"}
{$ld =1}
{config_load file="common.ini"}
<!DOCTYPE html>
<html lang="en-US">

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
                        <h1 class="page-header">{#ManageDevice#}</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                {#SearhDevice#}
                            </div>
                            <div class="panel-body">
                                <div class ="row">
                                    <div class="col-md-4">
                                        <div class="form-group input-group">
                                            <input type="text" class="form-control" placeholder="{#DevSearchIntro#}" id="form-usearch">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="btn-dev-search"><i class="fa fa-search"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary" type="button" id="btn-adddev"><i class="fa fa-plus"></i> {#DevNew#}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-10 col-md-12" id="devtable">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{#HID#}</th>
                                        <th>{#DevName#}</th>
                                        <th>{#Owner#}</th>
                                        <th>{#Auth#}</th>
                                        <th>{#Remove#}</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-users">
                                    {nocache}
                                    {if !isset($ld)}{config_load file="admin.ini" section="device"}{/if}
                                    {foreach $devices as $device}
                                        <tr>
                                            <td>{$device.id}</td>
                                            <td>{$device.hid}</td>
                                            <td>{$device.name}<button class="btn  btn-primary btn-xs pull-right edname-btn" type="button" data-devid="{$device.id}"><i class="fa fa-pencil"></i></button></td>
                                            <td>{$device.owner}<button class="btn  btn-primary btn-xs pull-right edowner-btn" type="button" data-devid="{$device.id}"><i class="fa fa-pencil"></i></button></td>
                                            <td><button class="btn  btn-info btn-xs edauth-btn" type="button" data-devid="{$device.id}">{#ViewDetails#}</button></td>
                                            <td><button type="button" class="btn btn-danger btn-xs dremove-btn" data-devid="{$device.id}"><i class="fa fa-remove"></i></button></td>
                                        </tr>
                                        {if $device@index>29}
                                            <tr>
                                                <td colspan="6">{#UseSearch#}</td>
                                            </tr>
                                            {break}
                                        {/if}
                                    {/foreach}
                                {/nocache}
                            </tbody>
                        </table>
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

    <!-- Custom Theme JavaScript -->
    <script src="static/js/scripts-control.js"></script>


</body>

</html>
