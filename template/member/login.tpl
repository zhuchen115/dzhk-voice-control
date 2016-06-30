{config_load file="member.ini" section="login"}
{config_load file="common.ini"}
<!DOCTYPE html>
<html lang="en">

    <head>

        {include file="common/header.tpl"}
        <title>{#LoginPage#}-{#CompanyName#}</title>
        <link rel="stylesheet" href="static/css/login-style.css">
        <link rel="stylesheet" href="static/css/form-elements.css">

    </head>

    <body>

        <!-- Top content -->
        <div class="top-content">

            <div class="inner-bg">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2 text">
                            <h1><strong>{#CompanyName#}</strong>{#LoginPage#}</h1>
                            <div class="description">
                                <p>
                                    {#Description#}
                                </p>
                            </div>
                                <div id ="logininfo" style ="display :none;">
                                    <div class="alert alert-danger" role="alert"><strong>{#LoginError#}</strong>{#InvaildLogin#}</div>
                                </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3 form-box">
                            <div class="form-top">
                                <div class="form-top-left">
                                    <h3>{#LoginDescription#}</h3>
                                    <p>{#Instruction#}</p>
                                </div>
                                <div class="form-top-right">
                                    <i class="fa fa-lock"></i>
                                </div>
                            </div>
                            <div class="form-bottom">
                                <form role="form" class="login-form">
                                    <div class="form-group">
                                        <label class="sr-only" for="form-username">{#Username#}</label>
                                        <input type="text" name="form-username" placeholder="{#Username#}..." class="form-username form-control" id="form-username">
                                    </div>
                                    <div class="form-group">
                                        <label class="sr-only" for="form-password">{#Password#}</label>
                                        <input type="password" name="form-password" placeholder="{#Password#}..." class="form-password form-control" id="form-password">
                                    </div>
                                    <button type="button" class="btn" id ="btn-submit" >{#SignIn#}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {include file="common/footer.tpl"}
        <script src="static/js/jquery.backstretch.min.js"></script>
        <script src="static/js/scripts-login.js"></script>

        <!--[if lt IE 10]>
            <script src="static/js/placeholder.js"></script>
        <![endif]-->

    </body>

</html>
