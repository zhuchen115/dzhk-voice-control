{config_load file="member.ini" section="ucenter"}
{config_load file="member.ini" section="settings"}
{config_load file="common.ini"}
<!DOCTYPE html>
<html lang="en">

    <head>

        {include file="common/header.tpl"}
        <meta name="description" content="">
        <meta name="author" content="">

        <title>{#UserSetting#}-{#ControlPanel#}-{#CompanyName#}</title>


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
                        <h1 class="page-header">{#UserSetting#}</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                {#PasswdEmail#}
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        
                                        <form role="form" id="form-passwd">
                                            <div class="form-group">
                                                <label>{#Username#}</label>
                                                <p class="form-control-static">{$user.username}</p>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">{#Email#}</label>
                                                <input type="text" name="email" id="form-email" class="form-control" value="{$user.email}">
                                            </div>
                                            <div class="form-group" id="fg-opassword">
                                                <label class="control-label">{#OldPassword#}</label>
                                                <input type="password" name="opassword" id="form-opassword" class="form-control" >
                                                <p class="help-block">{#PasswordNotice#}</p>
                                            </div>
                                            <div class="form-group" id="fg-npassword">
                                                <label class="control-label">{#Password#}</label>
                                                <input type="password" name="password" id="form-password" class="form-control" >
                                            </div>
                                            <div class="form-group" id="fg-rpassword">
                                                <label class="control-label">{#RepeatPassword#}</label>
                                                <input type="password" name="rpassword" id="form-rpassword" class="form-control" >
                                            </div>
                                            <button type="button" class="btn btn-default" id="btn-chpasswd">{#Submit#}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                            {#UserInfo#}
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <form role="form" id="form-uinfo">
                                            <div class="form-group">
                                                <label>{#DispName#}</label>
                                                <input type="text" id="input-dispname" class="form-control" value="{if isset($user.extra.name)}{$user.extra.name}{/if}" data-utype="name">
                                                <p class="help-block" >{#DescDispName#}</p>
                                            </div>
                                            <button type="button" class="btn btn-default" id="btn-userinfo">{#Submit#}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
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

        <!-- Custom Theme JavaScript -->
        <script src="static/js/scripts-control.js"></script>
        <script src="static/js/scripts-usetting.js"></script>
    </body>

</html>
