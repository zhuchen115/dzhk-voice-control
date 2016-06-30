{config_load "ucenter.ini" "nav-side"}
{$incache=1}
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li class="sidebar-search">
                <div class="input-group custom-search-form">
                    <input type="text" class="form-control" placeholder="{#Search#}...">
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
                <!-- /input-group -->
            </li>
            <li>
                <a href="index.php"><i class="fa fa-dashboard fa-fw"></i> {#Dashboard#}</a>
            </li>
            <li>
                <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> {#DataCharts#}<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="#">{#HealthData#}</a>
                    </li>
                    <li>
                        <a href ="#">{#SensorData#}</a>
                    </li>
                </ul>
                <!-- /.nav-second-level -->
            </li>           
            <li>
                <a href="#"><i class="fa fa-wrench fa-fw"></i> {#UserSetting#}<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li>
                        <a href="?action=setting">{#AccountSetting#}</a>
                    </li>
                    <li>
                        <a href="?action=device">{#UserDevices#}</a>
                    </li>
                    <li>
                        <a href="#">{#Messages#}</a>
                    </li>
                </ul>
            </li>
            {nocache}
                {if !empty($smarty.session.admin)}
                    {if !isset($incache)}{config_load "ucenter.ini" "nav-side"}{/if}
                    <li>
                        <a href="#"><i class="fa fa-wrench fa-fw"></i>{#AdminPanel#}<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                                <li>
                                    <a href="?class=admin&action=user">{#ManageUser#}</a>
                                </li>
                                <li>
                                    <a href="?class=admin&action=device">{#ManageDevice#}</a>
                                </li>
                            
                        </ul>
                    </li>
                {/if}
                {/nocache}
                <li></li>
                <li><a href="https://www.greatqq.com/" target="_blank"> Powered by</a></li>
        </ul>
    </div>
</div>