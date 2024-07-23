var fixedLength = 0;
jQuery.validator.addMethod("filesize_max", function (value, element, param) {
    var isOptional = this.optional(element),
        file;
    if (isOptional) {
        return isOptional;
    }
    if ($(element).attr("type") === "file") {
        if (element.files && element.files.length) {
            file = element.files[0];
            return (file.size && file.size <= 52428800);
        }
    }
    return false;
}, "File size is too large.");

$.validator.addMethod('dimension', function (value, element, param) {
    if (element.files.length == 0) {
        return true;
    }
    var file = element.files[0];
    var width = height = 0;
    var tmpImg = new Image();
    var result = '';
    tmpImg.src = window.URL.createObjectURL(file);
    tmpImg.onload = function () {
        width = tmpImg.naturalWidth,
            height = tmpImg.naturalHeight;

        console.log(width);
        console.log(height);
        result = (width <= param[0] && height <= param[1]);
        console.log(result);
        return result;
    }
}, function () {
    return 'Please upload an image with maximum 100 x 100 pixels dimension'
});

jQuery.validator.addMethod("fixedDigits", function (value, element, param) {
    var isOptional = this.optional(element);
    fixedLength = param;

    if (isOptional) {
        return isOptional;
    }

    return ($(element).val().length <= param);
}, function () {
    return "Value cannot exceed " + fixedLength + " characters."
});

jQuery.validator.addMethod("extension", function (value, element, param) {
    param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
    return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
}, "Please select image with a valid extension (.jpg, .jpeg, .png, .gif, .svg)");

jQuery.validator.addMethod("import_extension", function (value, element, param) {
    param = typeof param === "string" ? param.replace(/,/g, '|') : "xls|xlsx";
    return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
}, "Please select file with a valid extension (.xls, .xlsx)");

jQuery.validator.addMethod("docextension", function (value, element, param) {
    param = typeof param === "string" ? param.replace(/,/g, '|') : "png|jpe?g|gif";
    return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
}, "Please select file with a valid extension (.jpg, .jpeg, .png, .doc, .docx, .pdf)");

jQuery.validator.addMethod("decimalPlaces", function (value, element) {
    return this.optional(element) || /^\d+(\.\d{0,2})?$/i.test(value);
}, "Please enter a value with maximum two decimal places.");

jQuery.validator.addMethod("alphanumeric", function (value, element) {
    return this.optional(element) || /^[a-zA-Z0-9]+$/i.test(value);
}, "Please enter alphanumeric value.");

jQuery.validator.addMethod("alphanumericspace", function (value, element) {
    return this.optional(element) || /^[a-zA-Z0-9\s]+$/i.test(value);
}, "Please enter alphanumeric value.");

jQuery.validator.addMethod("exactlength", function (value, element, param) {
    return this.optional(element) || value.length == param;
}, $.validator.format("Please enter exactly {0} characters."));

jQuery.validator.addMethod("lettersonly", function (value, element) {
    return this.optional(element) || /^[a-zA-Z\s]+$/i.test(value);
}, "Name can have alphabets and space only.");

jQuery.validator.addMethod("contact_number", function (value, element) {
    return this.optional(element) || /^\+[0-9]+[0-9\-]+[0-9]+$/i.test(value);
}, "Incorrect number format");

jQuery.validator.addMethod("non_whitespace", function (value, element) {
    return this.optional(element) || /^(?!\s*$).+/i.test(value);
}, "Incorrect value");

jQuery.validator.addMethod("check_content", function (value, el, param) {
    var content = $(el).summernote('code');
    content = $(content).text().replace(/\s+/g, '');

    return (content !== "");
}, "Incorrect value");

jQuery.validator.addMethod("correctPassword", function (value, element) {
    return this.optional(element) || /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{6,}$/i.test(value);
}, "Please fill minimum 6 character Password with uppercase, lowercase, special character and digit");

$.validator.addMethod("greaterThanDate", function (value, element, param) {
    var $otherElement = $(param);
    return new Date('1970-01-01T' + value + 'Z') > new Date('1970-01-01T' + $otherElement.val() + 'Z');
}, "End Time must be greater than start time");


jQuery.validator.addMethod("validate_email", function (value, element) {
    if (/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(value)) {
        return true;
    } else {
        return false;
    }
}, "Please enter a valid Email.");


jQuery.validator.addMethod("alphaspace", function (value, element) {
    if (/^([a-zA-Z_\.\-])+\@(([a-zA-Z\-])+\.)+([a-zA-Z]{2,4})+$/.test(value)) {
        return true;
    } else {
        return false;
    }
}, "Please enter a valid Email.");



var form_validation = function () {
    // alert('enter');
    var e = function () {
        var form_validate = jQuery(".form-valide").validate({
            ignore: [".note-editor *", "password"],
            errorClass: "invalid-feedback animated fadeInDown",
            errorElement: "div",
            errorPlacement: function (e, a) {
                jQuery(a).closest(".form-group").append(e)
            },
            highlight: function (e) {
                jQuery(e).closest(".form-group").removeClass("is-invalid").addClass("is-invalid")
            },
            success: function (e) {
                jQuery(e).closest(".form-group").removeClass("is-invalid"), jQuery(e).remove()
            },
            rules: {
                "email": {
                    required: !0,
                    validate_email: !0,
                    remote: APP_NAME + "/admin/admins/checkAdmins"
                },
                "user_email": {
                    required: !0,
                    validate_email: !0,
                    remote: APP_NAME + "/admin/users/checkUsers"
                },
                "kitchen_email": {
                    required: !0,
                    validate_email: !0,
                    remote: APP_NAME + "/admin/kitchens/checkKitchens"
                },
                "tablet_email": {
                    required: !0,
                    validate_email: !0,
                    remote: APP_NAME + "/admin/tablets/checkTablets"
                },
                "password": {
                    required: !0,
                    minlength: 6,
                    correctPassword: !0
                },
                "confirm-password": {
                    required: !0,
                    equalTo: "#password",
                    correctPassword: !0
                },
                "old-pass": {
                    required: !0,
                },
                "pass": {
                    required: !0,
                    minlength: 6,
                    correctPassword: !0
                },
                "confirm-pass": {
                    required: !0,
                    equalTo: "#pass",
                    correctPassword: !0
                },
                "name": {
                    required: !0,
                    lettersonly: !0,
                    maxlength: 100,
                    minlength: 3
                },
                "customer_name": {
                    required: !0,
                    lettersonly: !0,
                    maxlength: 100,
                    minlength: 3
                },
                "brand_name": {
                    required: !0,
                    //lettersonly: !0,
                    maxlength: 100,
                    minlength: 2
                },
                "item_name": {
                    required: !0,
                    //lettersonly: !0,
                    maxlength: 100,
                    minlength: 2
                },
                "title": {
                    required: !0,
                    //lettersonly: !0,
                    maxlength: 100,
                    minlength: 3
                },
                "dob": {
                    required: !0
                },
                "gender": {
                    required: !0
                },
                "marital_status": {
                    required: !0
                },
                "last_name": {
                    required: !0,
                    //lettersonly: !0,
                    maxlength: 100,
                    minlength: 3
                },
                "article_name": {
                    required: !0,
                    //lettersonly: !0,
                    maxlength: 100,
                    minlength: 3,
                    remote: APP_NAME + "/admin/articles/checkArticle"
                },
                "article_image": {
                    extension: "jpeg|png|jpg|gif|svg",
                    filesize_max: !0
                },
                "gallery_image[]": {
                    filesize_max: !0
                },
                "user_image": {
                    extension: "jpeg|png|jpg|gif|svg",
                    filesize_max: !0
                },
                "kitchen_image": {
                    extension: "jpeg|png|jpg|gif|svg",
                    filesize_max: !0
                },
                "tablet_image": {
                    extension: "jpeg|png|jpg|gif|svg",
                    filesize_max: !0
                },
                "splashscreen_name": {
                    required: !0,
                    maxlength: 100,
                    minlength: 3,
                    // remote: APP_NAME + "/admin/splashscreens/checkSplashScreen"
                },
                "splashscreen_image": {
                    extension: "jpeg|png|jpg|gif|svg",
                    filesize_max: !0
                },
                "church_email": {
                    required: !0,
                    validate_email: !0
                },
                "church_name": {
                    required: !0,
                    maxlength: 100,
                    minlength: 3
                },
                "language_name": {
                    required: !0,
                    // lettersonly: !0,
                    maxlength: 100,
                    minlength: 3,
                    remote: APP_NAME + "/admin/languages/checkLanguage"
                },
                "ministrie_name": {
                    required: !0,
                    // lettersonly: !0,
                    maxlength: 100,
                    minlength: 3,
                    remote: APP_NAME + "/admin/ministries/checkMinistrie"
                },
                "size_name": {
                    required: !0,
                    // lettersonly: !0,
                    maxlength: 100,
                    minlength: 3,
                    remote: APP_NAME + "/admin/sizes/checkSize"
                },
                "ethnicity_name": {
                    required: !0,
                    // lettersonly: !0,
                    maxlength: 100,
                    minlength: 3,
                    remote: APP_NAME + "/admin/ethnicitys/checkEthnicity"
                },
                "advertisement_image": {
                    extension: "jpeg|png|jpg|gif|svg",
                    //required: !0
                    filesize_max: !0
                },

                "time_content": {
                    required: !0
                },
                "address": {
                    required: !0
                },
                "zipcode": {
                    required: !0,
                    //number: !0
                },
                "families_id[]": {
                    required: true
                },
                "countries_id[]": {
                    required: !0
                },
                "states_id[]": {
                    required: !0
                },
                "cities_id[]": {
                    required: !0
                },
                "region_id[]": {
                    required: !0
                },
                "related_parent_id[]": {
                    required: !0
                },
                "user_id[]": {
                    required: !0
                },
                "kitchen_id[]": {
                    required: !0
                },
                "tablet_id[]": {
                    required: !0
                },
                "users[]": {
                    required: !0
                },
                'auto_deactive': {
                    required: !0
                },
                "phone_code": {
                    required: !0,
                    //  number: !0
                },
                "customer_mobile_number": {
                    required: !0,
                    number: !0,
                    minlength: 8,
                    maxlength: 12
                },
                "phone_number": {
                    required: !0,
                    number: !0,
                    minlength: 8,
                    maxlength: 12
                },
                "mobile": {
                    required: !0,
                    number: !0,
                    minlength: 8,
                    maxlength: 12
                },
                "mobile_number": {
                    required: !0,
                    number: !0,
                    minlength: 8,
                    maxlength: 12
                },
                "description": {
                    required: !0
                },
                "country_shortname": {
                    required: !0,
                    maxlength: 3,
                    minlength: 2,

                },
                "country_name": {
                    required: !0,
                    maxlength: 100,
                    minlength: 3,
                    remote: APP_NAME + "/admin/countries/checkCountryName"
                },
                "country_code": {
                    required: !0,
                    minlength: 2,
                },
                "country_id": {
                    required: !0,
                },
                "state_name": {
                    required: !0,
                    maxlength: 100,
                    lettersonly: !0,
                    minlength: 2,
                    remote: APP_NAME + "/admin/states/checkStatesName"
                },
                "state_id": {
                    required: !0
                },
                "city_name": {
                    required: !0,
                    maxlength: 100,
                    lettersonly: !0,
                    minlength: 2,
                    remote: APP_NAME + "/admin/cities/checkCitiesName"
                },
                "city_id": {
                    required: !0
                },
                "region_id": {
                    required: !0
                },
                "region_name": {
                    required: !0,
                    maxlength: 100,
                    lettersonly: !0,
                    minlength: 2,
                    remote: APP_NAME + "/admin/regions/checkRegionsName"
                },
                "user_id": {
                    required: !0,
                },
                 "kitchen_id": {
                    required: !0,
                },
                "tablet_id": {
                    required: !0,
                },
                "item_category_id": {
                    required: !0,
                },
                "families_name": {
                    required: !0,
                    maxlength: 100,
                    lettersonly: !0,
                    minlength: 2,
                    remote: APP_NAME + "/admin/families/checkFamiliesName"
                },
                "position": {
                    required: !0
                },
                'hair_color': {
                    required: !0
                },
                'nationality': {
                    required: !0
                },
                "tutorialscreen_name": {
                    required: !0,
                    maxlength: 100,
                    minlength: 3,
                    // remote: APP_NAME + "/admin/splashscreens/checkSplashScreen"
                },
                "price": {
                    required: !0,
                    maxlength: 7,
                    minlength: 2,
                    number: !0

                },
                'start_date': {
                    required: !0
                },
                'end_date': {
                    required: !0
                },

            },
            messages: {
                "email": {
                    required: "Please provide email address",
                    validate_email: "Please enter a valid email address",
                    remote: "This email is already taken."
                },
                "user_email": {
                    required: "Please provide email address",
                    validate_email: "Please enter a valid email address",
                    remote: "This email is already taken."
                },
                "kitchen_email": {
                    required: "Please provide email address",
                    validate_email: "Please enter a valid email address",
                    remote: "This email is already taken."
                },
                "tablet_email": {
                    required: "Please provide email address",
                    validate_email: "Please enter a valid email address",
                    remote: "This email is already taken."
                },
                "dob": {
                    required: "Please select date of birth"
                },
                "gender": {
                    required: "Please provide select gender"
                },
                "marital_status": {
                    required: "Please provide select marital status"
                },
                "password": {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long"
                },
                "confirm-password": {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 6 characters long",
                    equalTo: "Please enter the same password as above"
                },
                "old-pass": {
                    required: "Please provide a old password"
                },
                "pass": {
                    required: "Please provide new password",
                    minlength: "Your password must be at least 6 characters long"
                },
                "confirm-pass": {
                    required: "Please provide confirm password",
                    minlength: "Your password must be at least 6 characters long",
                    equalTo: "Please enter the same password as above"
                },
                "customer_name": {
                    required: "Please provide name",
                    // lettersonly: "Please provide lettersonly",
                    maxlength: "Your name max 20 characters long",
                    minlength: "Your name at least 3 characters long"
                },
                "name": {
                    required: "Please provide name",
                    // lettersonly: "Please provide lettersonly",
                    maxlength: "Your name max 20 characters long",
                    minlength: "Your name at least 3 characters long"
                },
                "brand_name": {
                    required: "Please provide brand name",
                    // lettersonly: "Please provide lettersonly",
                    maxlength: "Your name max 50 characters long",
                    minlength: "Your name at least 2 characters long"
                },
                "item_name": {
                    required: "Please provide item name",
                    // lettersonly: "Please provide lettersonly",
                    maxlength: "Your name max 50 characters long",
                    minlength: "Your name at least 2 characters long"
                },
                "title": {
                    required: "Please provide title",
                    // lettersonly: "Please provide lettersonly",
                    maxlength: "Your first name max 100 characters long",
                    minlength: "Your first name at least 3 characters long"
                },
                "last_name": {
                    required: "Please provide last name",
                    // lettersonly: "Please provide lettersonly",
                    maxlength: "Your last name max 20 characters long",
                    minlength: "Your last name at least 3 characters long"
                },

                "user_id": {
                    required: "Please provide user"
                },
                "kitchen_id": {
                    required: "Please provide kitchen user"
                },
                "tablet_id": {
                    required: "Please provide tablet user"
                },
                "item_category_id": {
                    required: "Please select category"
                },
                "nationality": {
                    required: 'Please enter nationality'
                },
                "families_name": {
                    required: "Please provide name",
                    // lettersonly: "Please provide lettersonly",
                    maxlength: "Your name max 100 characters long",
                    minlength: "Your name at least 3 characters long",
                    remote: "This name is already taken."
                },

                "user_image": {
                    required: "Please provide image"
                },
                "kitchen_image": {
                    required: "Please provide image"
                },
                "tablet_image": {
                    required: "Please provide image"
                },
                "splashscreen_name": {
                    required: "Please provide splash screen name",
                    maxlength: "Your splash screen name max 100 characters long",
                    minlength: "Your splash screen name at least 3 characters long",
                    //remote: "This splash screen name is already taken."
                },
                "splashscreen_image": {
                    required: "Please provide image"
                },
                "church_email": {
                    required: "Please provide email address",
                    validate_email: "Please enter a valid email address"
                },

                "church_name": {
                    required: "Please provide name",
                    // lettersonly: "Please provide lettersonly",
                    maxlength: "Your name max 100 characters long",
                    minlength: "Your name at least 3 characters long"
                },
                "language_name": {
                    required: "Please provide name",
                    // lettersonly: "Please provide lettersonly",
                    maxlength: "Your language name max 100 characters long",
                    minlength: "Your language name at least 3 characters long",
                    remote: "This language is already taken."
                },
                "ministrie_name": {
                    required: "Please provide name",
                    // lettersonly: "Please provide lettersonly",
                    maxlength: "Your ministrie name max 100 characters long",
                    minlength: "Your ministrie name at least 3 characters long",
                    remote: "This ministrie is already taken."
                },
                "size_name": {
                    required: "Please provide name",
                    // lettersonly: "Please provide lettersonly",
                    maxlength: "Your size name max 100 characters long",
                    minlength: "Your size name at least 3 characters long",
                    remote: "This size is already taken."
                },
                "ethnicity_name": {
                    required: "Please provide name",
                    // lettersonly: "Please provide lettersonly",
                    maxlength: "Your ethnicity name max 100 characters long",
                    minlength: "Your ethnicity name at least 3 characters long",
                    remote: "This ethnicity is already taken."
                },
                "advertisement_image": {
                    required: "Please provide advertisement image"
                },

                "time_content": {
                    required: "Please provide time"
                },
                "address": {
                    required: "Please provide address"
                },
                "zipcode": {
                    required: "Please provide zip code",
                    // number: "Please enter a valid number format ex. 123"
                },
                "families_id[]": {
                    required: "Please provide family"
                },
                "countries_id[]": {
                    required: "Please provide country"
                },
                "states_id[]": {
                    required: "Please provide state"
                },
                "cities_id[]": {
                    required: "Please provide city"
                },
                "user_id[]": {
                    required: "Please provide user"
                },
                "kitchen_id[]": {
                    required: "Please provide user"
                },
                "tablet_id[]": {
                    required: "Please provide user"
                },
                "users[]": {
                    required: "Please provide user"
                },
                "region_id[]": {
                    required: "Please provide village/region"
                },
                "related_parent_id[]": {
                    required: "Please provide replated parent user"
                },
                'auto_deactive': {
                    required: "Please provide auto deactive"
                },
                "phone_code": {
                    required: "Please provide phone code ex. 971",
                    number: "Please enter a valid number format ex. 123"
                },
                "customer_mobile_number": {
                    required: "Please provide phone number ex. 88888888888",
                    number: "Please enter a valid number format ex. 123",
                    maxlength: "Your phone number max 12 characters long",
                    minlength: "Your phone number at least 8 characters long"
                },
                "phone_number": {
                    required: "Please provide phone number ex. 88888888888",
                    number: "Please enter a valid number format ex. 123",
                    maxlength: "Your phone number max 12 characters long",
                    minlength: "Your phone number at least 8 characters long"
                },
                "mobile": {
                    required: "Please provide phone number ex. 88888888888",
                    number: "Please enter a valid number format ex. 123",
                    maxlength: "Your phone number max 12 characters long",
                    minlength: "Your phone number at least 8 characters long"
                },
                "mobile_number": {
                    required: "Please provide phone number ex. 88888888888",
                    number: "Please enter a valid number format ex. 123",
                    maxlength: "Your phone number max 12 characters long",
                    minlength: "Your phone number at least 8 characters long"
                },
                "description": {
                    required: "Please provide description"
                },
                "country_shortname": {
                    required: "Please provide country short name",
                    maxlength: "Your shortname max 3 characters long",
                    minlength: "Your shortname at least 2 characters long"
                },
                "country_name": {
                    required: "Please provide country name",
                    maxlength: "Your name max 100 characters long",
                    minlength: "Your name at least 3 characters long",
                    remote: "This country name is already taken."
                },
                "country_code": {
                    required: "Please provide country code",
                    minlength: "Your country code at least 2 characters long",
                    remote: "country code already in use!"
                },
                "country_id": {
                    required: "Please select country"
                },
                "region_id": {
                    required: "Please select region"
                },
                "hair_color": {
                    required: "Please select heir color"
                },
                "state_name": {
                    required: "Please provide state name",
                    lettersonly: "Please provide lettersonly",
                    maxlength: "Your name max 100 characters long",
                    minlength: "Your name at least 2 characters long",
                    remote: "This state name is already taken."
                },
                "state_id": {
                    required: "Please select state"
                },
                "city_name": {
                    required: "Please provide city name",
                    lettersonly: "Please provide lettersonly",
                    maxlength: "Your name max 100 characters long",
                    minlength: "Your name at least 2 characters long",
                    remote: "This city name is already taken."
                },
                "city_id": {
                    required: "Please select city"
                },
                "region_name": {
                    required: "Please provide region name",
                    lettersonly: "Please provide lettersonly",
                    maxlength: "Your name max 100 characters long",
                    minlength: "Your name at least 2 characters long",
                    remote: "This region name is already taken."
                },
                'position': {
                    required: "Please provide position"
                },

                "tutorialscreen_name": {
                    required: "Please provide tutorial screen name",
                    maxlength: "Your tutorial screen name max 100 characters long",
                    minlength: "Your tutorial screen name at least 3 characters long",
                    //remote: "This splash screen name is already taken."
                },

                "price": {
                    required: "Please provide price",
                    maxlength: "Your price max 6 characters long",
                },
                "start_date": {
                    required: "Please select start date",
                },
                "end_date": {
                    required: "Please select end date",
                }



            }
        })
    }
    return {
        init: function () {
            e(); jQuery(".form-control").on("change", function () {
                jQuery(this).valid()
            });
            jQuery("input[type=file]").on("change", function () {
                jQuery(this).valid();
            });
        }
    }
}();
jQuery(function () {
    form_validation.init()
});
