/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
$(document).ready(function() {
    $( "#dialog:ui-dialog" ).dialog( "destroy" );
    $( "#users" ).hide();
    $( "#posts" ).hide();
    $( "#user-profile" ).hide();
    checkURL();

    $(window).bind("hashchange", function() {
        var hash = window.location.hash;
        checkURL(hash);
    });

    var currentUser;
    var loggedUser = JSON.parse(localStorage.getItem("user"));
    if(loggedUser != null) {
        $.ajax({
            "type": "GET",
            "url" : serviceEndPoint + "/users/isAuthenticated/" + loggedUser.id + "/" + loggedUser.token,
            "statusCode": {
                200: function() {
                    $("#my-menu").show();
                    $("#login-register-menu").hide();
                    $( "#my-menu").empty();
                    $( "#my-menuTmpl" ).tmpl( loggedUser ).appendTo( "#my-menu" );
                },
                401: function() {
                    $( "#my-menu").empty();
                    $("#my-menu").hide();
                    $("#login-register-menu").show();
                }
            }
        });
    }

    $( "#register-form-dialog" ).dialog({
        autoOpen: false,
        modal: true,
        draggable: false,
        resizable: false,
        closeOnEscape: true,
        buttons: {
            "Register": function() {
                var bValid = true;
                if(bValid) {
                    $.ajax({
                        "type": "POST",
                        "url": serviceEndPoint + "/users/register",
                        "data": $("form#register-form").serialize(),
                        "dataType": "json",
                        "success": function(data) {
                            if(data.error != null) {
                                alert(data.error);
                            } else {
                                authenticate($("#register_form_email").val(), $("#register_form_password").val(), $( "#register-form-dialog" ));
                            }
                        }
                    });
                }
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
        }
    });

    $( "a#register-user" ).live("click", function(event) {
        event.preventDefault();
        $( "#register-form-dialog" ).dialog( "open" );
    });

    function authenticate(email, password, dialog) {
        $.ajax({
            "type": "POST",
            "url": serviceEndPoint + "/users/login",
            "data": {"email": email, "password": password},
            "dataType": "json",
            "async": false,
            "success": function(data) {
                 localStorage.setItem("user", JSON.stringify(data));
                 window.location.reload();
            },
            "statusCode": {
                 401: function() {
                     alert("Invalid login");
                }
            }
        });
    }

    $( "#login-form-dialog" ).dialog({
        autoOpen: false,
        modal: true,
        draggable: false,
        resizable: false,
        closeOnEscape: true,
        buttons: {
            "Login": function() {
                    authenticate($("#login_form_email").val(), $("#login_form_password").val(), $( "#login-form-dialog" ));
                },
                Cancel: function() {
                    $( this ).dialog( "close" );
                }
            },
            close: function() {
            }
    });

    $( "a#login-user" ).live("click", function(event) {
        event.preventDefault();
        $( "#login-form-dialog" ).dialog( "open" );
    });

    $( "a#logout" ).live("click", function(event) {
        event.preventDefault();
        localStorage.removeItem("user");
        $.ajax({
            "type": "GET",
            "url" : serviceEndPoint + "/users/logout",
            "success": function(data) {
                window.location.hash = "#home";
                window.location.reload();
            }
        });
    });

    $( "#compose-post-form-dialog" ).dialog({
        autoOpen: false,
        modal: true,
        draggable: false,
        resizable: false,
        closeOnEscape: true,
        width: 500,
        buttons: {
            "Post It": function() {
                var message = $( "textarea#compose_message" ).val();
                if(!loggedUser) {
                    alert("You are not logged in!");
                    $( "#compose-post-form-dialog" ).dialog( "close" );
                    return;
                }
                if(message.length>0) {
                    $.ajax({
                        "type": "POST",
                        "url": serviceEndPoint + "/posts/add",
                        "data": {"message": message, "user_id": loggedUser.id, "token": loggedUser.token},
                        "dataType": "json",
                        "success": function(data) {

                        },
                        "statusCode": {
                            200: function(data) {
                                $( "#compose-post-form-dialog" ).dialog( "close" );
                                if(currentUser!=null && currentUser.slug == loggedUser.slug) {
                                    window.locatiom.reload();
                                } else {
                                    window.location.hash = "#!/" + loggedUser.slug;
                                }
                            },
                            401: function() {
                                $( "#compose-post-form-dialog" ).dialog( "close" );
                                alert("Invalid login");
                        }
            }
                    });
                }
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        },
        open: function() {
            $( "textarea#compose_message" ).val("");
        },
        close: function() {
        }
    });

    var postOptions = {
        "maxCharacterSize": 160,
        "originalStyle": "originalDisplayInfo",
        "warningStyle": "warningDisplayInfo",
        "warningNumber": 40,
        "displayFormat": "#input Characters | #left Characters Left | #words Words"
    };

    $( "textarea#compose_message" ).textareaCount(postOptions);

    $( "a#compose-new-post" ).live("click", function(event) {
        event.preventDefault();
        $( "#compose-post-form-dialog" ).dialog( "open" );
    });

    $( "a#follow-user" ).live("click", function(event) {
        event.preventDefault();
        followUser($(this).data("slug"))
    });

    $( "a#unfollow-user" ).live("click", function(event) {
        event.preventDefault();
        unFollowUser($(this).data("slug"))
    });
})

var lastUrl = "";

function checkURL(hash) {
    if(!hash) hash=window.location.hash;
    if(hash != lastUrl) {
        lastUrl=hash;
    }
    loadPage(hash);
}

function loadPage(hash) {
    currentUser = null;
    $( "#user-profile" ).hide();
    $( "#users" ).hide();
    $( "#posts" ).hide();
    var action = null;
    var user_slug = null;
    if(hash.indexOf("#!/") == 0) {
        hash = hash.replace("#!/","");
        if(hash.indexOf("/") > 0 ) {
            user_slug = hash.substring(0, hash.indexOf("/"));
            action = hash.substring(hash.indexOf("/"));
        } else {
            user_slug = hash.substring(hash.indexOf("/"));
        }

        if(action !=null) {
            if(action.indexOf("following")>0) {
                return loadFollowingUsers(user_slug);
            } else if (action.indexOf("followers")>0) {
                return loadFollowers(user_slug);
            }
        }
        return loadPosts(user_slug);
    }
    return loadPosts();
}

function showCurrentUserProfile(currentUser) {
    if(currentUser.id != null) {
        $( "#user-profile" ).empty();
        $( "#currentUserTmpl" ).tmpl(currentUser).appendTo( "#user-profile" );
        $( "#user-profile" ).show();
    }
}

function followUser(slug) {
    if(typeof slug == "undefined")
        return;
    $.ajax({
        "type": "GET",
        "url" : serviceEndPoint + "/users/follow/" + slug,
        "dataType": "json",
        "success": function(data) {
            window.location.reload();
        }
    });
}

function unFollowUser(slug) {
    if(typeof slug == "undefined")
        return;
    $.ajax({
        "type": "GET",
        "url" : serviceEndPoint + "/users/unfollow/" + slug,
        "dataType": "json",
        "success": function(data) {
            window.location.reload();
        }
    });
}

function loadFollowingUsers(slug) {
    if(typeof slug == "undefined")
        return;
    slug = (typeof slug == "undefined") ? "" : slug;
    $.ajax({
        "type": "GET",
        "url" : serviceEndPoint + "/users/following/" + slug,
        "dataType": "json",
        "success": function(data) {
            showCurrentUserProfile(data.current_user)
            $( "#posts").empty();
            $( "#posts" ).hide();
            $( "#users" ).show();
            $( "#users" ).empty();
            $( "#userTmpl" ).tmpl( data.items ).appendTo( "#users" );
        }
    });
}

function loadFollowers(slug) {
    if(typeof slug == "undefined")
        return;
    slug = (typeof slug == "undefined") ? "" : slug;
    $.ajax({
        "type": "GET",
        "url" : serviceEndPoint + "/users/followers/" + slug,
        "dataType": "json",
        "success": function(data) {
            showCurrentUserProfile(data.current_user)
            $( "#posts").empty();
            $( "#posts" ).hide();
            $( "#users" ).show();
            $( "#users" ).empty();
            $( "#userTmpl" ).tmpl( data.items ).appendTo( "#users" );
        }
    });
}

function loadPosts(slug) {
    slug = (typeof slug == "undefined") ? "" : slug;
    $.ajax({
        "type": "GET",
        "url" : serviceEndPoint + "/posts/" + slug,
        "dataType": "json",
        "success": function(data) {
            $( "#posts" ).show();
            $( "#posts").empty();
            showCurrentUserProfile(data.current_user)
            $( "#postTmpl" ).tmpl( data.items ).appendTo( "#posts" );
        }
    });
}
