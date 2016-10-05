$(document).ready(function() {
    $('form:not(.filter) :input:visible:enabled:first').focus();
    $.validator.addMethod('pwchk', function(value) {
        return /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/.test(value);
    }, 'Use only letters, numbers, and the underscore. Must be at least 6 characters long.');
    $("#order_form").validate({
        rules: {
            fName: {
                required: true
            },
            lName: {
                required: true
            },
            street: {
                required: true
            },
            city: {
                required: true
            },
            state: {
                required: true,
                rangelength: [2, 2]
            },
            zip: {
                required: true,
                rangelength: [5, 10]
            },
            email: {
                required: true
            },
            ccDate: {
                required: true,
                date: true
            },
            ccNum: {
                required: true
            }
        }, // end of rules
        messages: {
            state: {
                rangelenth: "Please enter the state appreviation."
            },
            zip: {
                rangelength: "Please enter your zip with or without the 4 digit extension."
            },
            ccDate: {
                date: "Please enter your credit card expiration date in the format: mm/dd/yyyy."
            }
        }, // end of messages
        errorPlacement: function(error, element) {
            if (element.is(":radio") || element.is(":checkbox")) {
                error.appendTo(element);
            }
            else {
                error.insertAfter(element);
            }
        }
    }); // end validate()
    $("#change_pw").validate({
        rules: {
            password1: {
                required: true,
                pwchk: true
            },
            password2: {
                required: true,
                equalTo: '#password1',
                pwchk: true
            }
        },
        messages: {
            password1: {
                pwchk: "Passwords must contain at least six characters, including uppercase, lowercase letters and numbers."
            },
            password2: {
                equalTo: "Please enter your password a second time."
            }
        }
    });
    $("#forgot_pw").validate({
        rules: {
            email: {
                required: true
            }
        }
    });
    $("#login_form").validate({
        rules: {
            email: {
                required: true
            },
            pass: {
                required: true,
                pwchk: true
            }
        },
        messages: {
            pwchk: "Passwords must contain at least six characters, including uppercase, lowercase letters and numbers."
        }
    });
    $("#register_form").validate({
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            email: {
                required: true
            },
            password1: {
                required: function(element) {
                    return /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,}$/.test($('#password1').val());
                },
                pwchk: true
            },
            password2: {
                required: true,
                equalTo: '#password1',
                pwchk: true
            }
        },
        messages: {
            password1: {
                pwchk: "Passwords must contain at least six characters, including uppercase letters, lowercase letters, and numbers."
            },
            password2: {
                equalTo: "Please enter your password a second time."
            }
        }
    });

    $(".order_nums").change(function() {
        var sum = 0;
        $(".order_nums").each(function() {
            var value = parseInt($(this).val());
            if (!isNaN(value)) {
                sum += value;
            }
            $("#total").html(sum);
        });
    });
    $(".delete").onclick(function() {
        var chk = confirm("Are you sure you wish to delete this product?");
        if (chk)
            return true;
        else
            return false;
    });

    $("#delete-<?php echo $id; ?>").submit(function() {
        return confirm("Are you sure you want to delete this product??");
    });


    var fewSeconds = 5;
    $('form').submit(function() {
        var subButton = $(this).find(':submit');
        subButton.prop('disabled', true);
        subButton.val('Please wait...');
        setTimeout(function() {
            subButton.prop('disabled', false);
            subButton.val('Submit Order');
        }, fewSeconds * 1000);
    });

    // $("form").submit(function(event) {
    //    event.preventDefault();
    // });

    $("#login_page").lightbox_me({
        centered: true,
        closeClick: false,
        onLoad: function() {
            $('#login_form').find('input:first').focus();
        },
        overlayCSS: {
            background: 'black',
            opacity: 0.7
        }
    });
});
