{config_load "ucenter.ini" "nav-header"}
<ul class="nav navbar-top-links navbar-right">
    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-messages">
            {foreach $message as $msg nocache}
                {if $msg@index >3}
                    {break}
                {/if}
                <li>
                    <a href="#">
                        <div>
                            <strong>{$msg.from}</strong>
                            <span class="pull-right text-muted">
                                <em>{$msg.time|date_format:"%b %e %Y %H:%M:%S"}</em>
                            </span>
                        </div>
                        <div>$msg.msg</div>
                    </a>
                </li>

                <li class="divider"></li>

            {/foreach}

            <li>
                <a class="text-center" href="?action=message">
                    <strong>{#ReadMessage#}</strong>
                    <i class="fa fa-angle-right"></i>
                </a>
            </li>
        </ul>
        <!-- /.dropdown-messages -->
    </li>
    <!-- /.dropdown -->
    <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-user">
            <li><a href="?action=setting"><i class="fa fa-user fa-fw"></i> {#UserProfile#}</a>
            </li>
            <li><a href="?action=setting"><i class="fa fa-gear fa-fw"></i> {#Settings#}</a>
            </li>
            <li class="divider"></li>
            <li><a href="?action=logout"><i class="fa fa-sign-out fa-fw"></i> {#Logout#}</a>
            </li>
        </ul>
        <!-- /.dropdown-user -->
    </li>
    <!-- /.dropdown -->
</ul>