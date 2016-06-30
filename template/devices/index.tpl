{config_load file="common.ini"}
{config_load file="devices.ini" section="main"}
<!DOCTYPE html>
<html lang="en-US">

    <head>

        {include file="common/header.tpl"}
        <meta name="description" content="">
        <meta name="author" content="">

        <title>{#MyDevices#}-{#ControlPanel#}-{#CompanyName#}</title>


        {include file="common/header.tpl"}
        <!-- MetisMenu CSS -->
        <link href="static/css/metisMenu.min.css" rel="stylesheet">
        <link href="static/css/sb-admin-2.css" rel="stylesheet">
        <link href="static/css/datatables.min.css" rel="stylesheet">
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
        </div>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">{#MyDevices#}</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12" id ="col-devtable">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            {#DevicesList#}
                        </div>
                        <div class="panel-body">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <button type="button" id="btn-add" class="btn btn-primary"><i class="fa fa-plus"></i> {#AddDevice#}</button>  
                                </div>
                            </div>
                            <table class="table table striped table-bordered table-hover" id="table-devices">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{#HardwareAddress#}</th>
                                        <th>{#DeviceName#}</th>
                                        <th>{#DeviceAuthencation#}</th>
                                        <th>{#TransferOut#}</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody-device">
                                    {foreach $devices as $device nocache}
                                        <tr>
                                            <td>{$device.id}</td>
                                            <td>{$device.hid}</td>
                                            <td>{$device.name}<button type="button" class="btn btn-primary btn-sx pull-right name-btn" data-devid="{$device.id}"><i class="fa fa-pencil"></i></button></td>
                                            <td><button type="button" class="btn btn-primary btn-sm auth-btn" data-devid="{$device.id}">{#ViewDetails#}</button></td>
                                            <td><button type="button" class="btn btn-warning btn-sm shift-btn" data-devid="{$device.id}">{#ViewDetails#}</button></td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {include file="devices/add-device.tpl"}
        {include file="devices/change-name.tpl"}
        {include file="devices/device-auth.tpl"}
        {include file="devices/device-transfer.tpl"}
        {include file ="common/footer.tpl"}
        <!-- Metis Menu Plugin JavaScript -->
        <script src="static/js/metisMenu.min.js"></script>
        <!-- Custom Theme JavaScript -->
        <script src="static/js/scripts-control.js"></script>
        <script src="static/js/datatables.min.js" type="text/javascript"></script>
        <script src="static/js/scripts-devices.js" type="text/javascript"></script>
    </body>
</html>
