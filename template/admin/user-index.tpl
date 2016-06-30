{config_load file="common.ini"}
{config_load file="admin.ini" section="user"}
<!DOCTYPE html>
<html lang="en-US">

    <head>

        {include file="common/header.tpl"}
        <meta name="description" content="">
        <meta name="author" content="">

        <title>{#ManageUser#}-{#AdminPanel#}-{#CompanyName#}</title>


        {include file="common/header.tpl"}
        <!-- MetisMenu CSS -->
        <link href="static/css/metisMenu.min.css" rel="stylesheet">
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
                    <a class="navbar-brand" href="index.php">{#AdminPanel#}</a>
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
                    <h1 class="page-header">{#UserGroupManagement#}</h1>
                </div>
            </div>
            <div id="ug-info"></div>
            <div id="ug-tabs-wrapper">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#wr-users" aria-controls="users" role="tab" data-toggle="tab">{#Users#}</a></li>
                    <li role="presentation"><a href="#wr-groups" aria-controls="groups" role="tab" data-toggle="tab">{#Groups#}</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="wr-users">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                {#SearchUser#}
                            </div>
                            <div class="panel-body">
                                <div class ="row">
                                    <div class="col-md-4">
                                        <div class="form-group input-group">
                                            <input type="text" class="form-control" placeholder="{#USearchIntro#}" id="form-usearch">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="btn-user-search"><i class="fa fa-search"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary" type="button" id="btn-addu"><i class="fa fa-plus"></i> {#UserNew#}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12" id="utable">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>UID</th>
                                                <th>{#UserName#}</th>
                                                <th>{#Password#}</th>
                                                <th>{#Email#}</th>
                                                <th>{#Groups#}</th>
                                                <th>{#Name#}</th>
                                                <th>{#Auth#}</th>
                                                <th>{#Remove#}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-users">
                                            <tr><td colspan="8">{#UseSearch#}</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="wr-groups">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                {#SearchGroup#}
                            </div>
                            <div class="panel-body">
                                <div class ="row">
                                    <div class="col-md-4">
                                        <div class="form-group input-group">
                                            <input type="text" class="form-control" placeholder="{#GSearchIntro#}" id="form-gpsearch">
                                            <span class="input-group-btn">
                                                <button class="btn btn-default" type="button" id="btn-group-search"><i class="fa fa-search"></i></button>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-primary" type="button" id="btn-addg"><i class="fa fa-plus"></i> {#GroupNew#}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-lg-10" id="groupt-wrap">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>GID</th>
                                                <th>{#GroupName#}</th>
                                                <th>{#Auth#}</th>
                                                <th>{#GroupDesc#}</th>
                                                <th>{#Remove#}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody-groups">
                                            {nocache}
                                                {config_load "admin.ini" "groups"}
                                                {foreach $groups as $group}
                                                    {if in_array($group.id,[1,2,3,1000,1001])}
                                                        <tr>
                                                            <td>{$group.id}</td>
                                                            <td>{$group.name}</td>
                                                            <td><button class="btn  btn-primary btn-xs egauth-btn" type="button" data-gid="{$group.id}" disabled="disabled">{#ViewDetails#}</button></td>
                                                            <td>{$group.description}</td>
                                                            <td><button type="button" class="btn btn-danger btn-xs" data-uid="{$group.id}" disabled="disabled" data-toggle="tooltip" data-placement="right" title="{#NoRemove#}"><i class="fa fa-remove"></i></button></td>
                                                        </tr>
                                                    {else}
                                                        <tr>
                                                            <td>{$group.id}</td>
                                                            <td class="td-gname">{$group.name}<button class="btn  btn-primary btn-xs pull-right egname-btn" type="button" data-gid="{$group.id}"><i class="fa fa-pencil"></i></button></td>
                                                            <td><button class="btn  btn-info btn-xs egauth-btn" type="button" data-gid="{$group.id}">{#ViewDetails#}</button></td>
                                                            <td>{$group.description}<button class="btn  btn-success btn-xs pull-right egdesc-btn" type="button" data-gid="{$group.id}"><i class="fa fa-pencil"></i></button></td>
                                                            <td><button type="button" class="btn btn-danger btn-xs gremove-btn" data-gid="{$group.id}"><i class="fa fa-remove"></i></button></td>
                                                        </tr>
                                                    {/if}
                                                {/foreach}
                                            {/nocache}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>       
                    </div>
                </div>   


            </div>
        </div>
        {include file="admin/user-passwd.tpl"}
        {include file="admin/user-email.tpl"}
        {include file="admin/user-cname.tpl"}
        {include file="admin/user-remove.tpl"}
        {include file="admin/user-groups.tpl"}
        {include file="admin/user-auth.tpl"}
        {include file="admin/user-register.tpl"}
        {include file="admin/group-add.tpl"}
        {include file="admin/group-auth.tpl"}
        {include file ="common/footer.tpl"}
        <!-- Metis Menu Plugin JavaScript -->
        <script src="static/js/metisMenu.min.js"></script>
        <!-- Custom Theme JavaScript -->
        <script src="static/js/scripts-control.js"></script>
        <script src="static/js/datatables.min.js" type="text/javascript"></script>
        <script src="static/js/scripts-admin-users.js" type="text/javascript"></script>
    </body>
</html>
