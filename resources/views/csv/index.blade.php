{{-- Todo: tanaka bootstrapを使用するのが練習にもなるし、レスポンシブにもなってよいのでは？？ --}}
<form method="post" action="{{ route('csv::post') }}" enctype="multipart/form-data" id="csvUpload">
    {{ csrf_field() }}
    <div class="form-inner">
        <h2>認証用メールアドレス, パスワード</h2>
        <div>
            <div class="underline">
                <input type="email" name="email" data-validation="email" data-error="Please enter a valid email address" tabindex="2" id="email" autocorrect="off" autocapitalize="off" spellcheck="false">
                <label for="email">Email</label>
                <span class="placeholder">Email</span>
            </div>
        </div>
        <div class="half">
            <div class="underline">
                <input name="password" type="text" id="password" data-validation="password" data-error="No less than 6 characters" tabindex="4">
                <label for="password">Password</label>
                <span class="placeholder">Password</span>
            </div>
        </div>
        <h2>CSVアップロード</h2>
        <div class="half">
            <div class="uploadButton">
                CSVアップロード
                <input type="file" onchange="uv.style.display='inline-block'; uv.value = this.value;" name="csv_file">
                <input type="text" id="uv" class="uploadValue" disabled>
            </div>
        </div>
        <h2>認証方法</h2>
        <div class="half no-label">
            <div>
                <input type="radio" name="authentication" checked value="google" id="google">
                <label for="google">Google</label>
                <span class="radio" tabindex="11">
          <span></span>
        </span>
            </div>
        </div>
        <div class="half no-label">
            <div>
                <input type="radio" name="authentication" value="facebook" id="facebook">
                <label for="facebook">Facebook</label>
                <span class="radio" tabindex="12">
          <span></span>
        </span>
            </div>
        </div>
        <h2>Button</h2>
        <div class="buttons">
            <div><button type="submit" tabindex="13">送信する</button></div>
        </div>
    </div>
</form>

{{-- jqueryの呼び出し --}}
<script
        src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous">
</script>

<script>
    var space_key = typeof KeyEvent !== "undefined" ? KeyEvent.DOM_VK_SPACE : 32;
    var enter_key = typeof KeyEvent !== "undefined" ? KeyEvent.DOM_VK_ENTER : 13;
    var up_key =    typeof KeyEvent !== "undefined" ? KeyEvent.DOM_VK_UP    : 38;
    var down_key =  typeof KeyEvent !== "undefined" ? KeyEvent.DOM_VK_DOWN  : 40;

    function setup_compy_form(){
        var form = $("form");
        var inputs = form.find("input, textarea");
        var spans = form.find("span");
        var checkboxes = spans.filter(".checkbox");
        var radios = spans.filter(".radio");
        var radio_inputs = inputs.filter("[type=radio]");
        var checkbox_inputs = inputs.filter("[type=checkbox]");
        var spinner_arrows = form.find(".spinner-arrow");
        var underlines = form.find(".underline");
        var underline_inputs = underlines.children("input");
        var underline_labels = underlines.children("label");
        var textareas = inputs.filter("textarea");
        var dropdowns = form.find(".dropdown");
        // var dropdown_options = dropdowns.find("p");
        var labels = form.find("label");
        var dropdown_inputs = dropdowns.find("input");
        var selects = dropdowns.find("select");

        labels.mousedown(function(e){
            e.preventDefault();
        })

        selects.change(function(){
            var select = $(this);
            var container = select.siblings("div");
            set_dropdown_value(container, select.val());
        });

        function set_dropdown_value(container, value){
            var input = container.children("input");
            var placeholder = container.children(".placeholder");

            container.addClass("float-label");

            input.val(value);
            placeholder.hide();
        }

        dropdown_inputs
            .filter(".focus-helper")
            .blur(function(){
                $(this).closest(".dropdown").removeClass("open");
            });


        dropdowns
            .click(function(e){
                var dropdown = $(this);
                var select = dropdown.find("select");

                if(select.size() > 0){

                } else {
                    dropdown.toggleClass("open");

                    if(dropdown.hasClass("open")){
                        var label = dropdown.find("label");

                        if(label.hasClass("invalid")){
                            label.text(label.data("text"));
                            label.removeClass("invalid");
                        }
                        dropdown.find(".focus-helper").focus();

                    }
                }
            })
            .mousedown(function(e){
                if(!$(e.target).is("select")){
                    e.preventDefault();
                }
            })
            .blur(function(){
                var dropdown = $(this);
                dropdown.removeClass("open");
            })
            .find(".dropdown-box p")
            .mousedown(function(e){
                e.preventDefault();
            })
            .click(function(e){
                var option = $(this);
                var dropdown_box = option.closest(".dropdown-box");
                dropdown_box.removeClass("open");
                set_dropdown_value(dropdown_box.parent(), option.text());
            });



        radio_inputs
            .filter("[checked]")
            .each(function(){
                activate_radio_input($(this));
            });

        // load previous form data from local storage
        load_form_data();

        form.submit(function () {

            if (validate_all()) {
                var $this = $(this);
                var formData = $this.serialize();
                alert(formData);
                return false;
            } else {
                $('html,body').animate({
                    scrollTop: $(".invalid").offset().top - 10
                }, 'fast');
                return false;
            }
        });

        textareas.each(function(){
            var textarea = $(this);

            textarea.on('input change cut paste drop keyup', function(){
                $(this)
                    .height('auto')
                    .height(this.scrollHeight);
            });
        });

        checkboxes
            .click(function () {
                var checkbox = $(this);
                toggle_checkbox_value(checkbox);
                checkbox.focus();
            })
            .keydown(function (e) {
                // check if space is defined
                var key = e.which;

                // space is pressed on focus
                if (key === space_key || key === enter_key) {
                    e.preventDefault();
                    toggle_checkbox_value($(this));
                }
            })
            .siblings("label")
            .click(function () {
                $(this).siblings(".checkbox").focus();
            });

        radios
            .click(function () {
                var radio = $(this);
                activate_radio_input(radio.siblings("input"));
            })
            .keydown(function (e) {
                // check if space is defined
                var key = e.which;

                // space is pressed on focus
                if (key === space_key || key === enter_key) {
                    activate_radio_input($(this).siblings("input"));
                }
            })
            .siblings("label")
            .click(function () {
                var radio = $(this).siblings(".radio");
                activate_radio_input(radio.siblings("input"));
                radio.focus();
            })
            .mousedown(function (e) {
                e.preventDefault();
            });


        inputs
            .each(function () {
                check_label_state($(this));
            })
            .focus(function () {
                var input = $(this);
                set_label_state(input, true);
                $(this).parent().addClass("focused");
                set_input_label_as_valid(input.siblings("label"));
            })
            .blur(function () {
                var input = $(this);
                check_label_state(input);
                input.parent().removeClass("focused");
            });



        inputs.filter("[type=number]")
            .each(function(){
                // fool HTML validation, enable numeric keyboard, but not a spinner widget
                $(this).attr("type", "tel");
            })
            .keydown(function (e) {
                var key = e.which;
                var input = $(this);
                if (key === up_key) {
                    step_numeric_input(input.siblings(".up"));
                    e.preventDefault();
                } else if (key === down_key) {
                    step_numeric_input(input.siblings(".down"));
                    e.preventDefault();
                }
            });



        inputs.on("input text change paste drop", function () {
            set_input_label_as_valid($(this).siblings("label"));
        });

        var spinner_hold_timeout = 0;

        spinner_arrows
            .click(function () {
                step_numeric_input($(this));
            })
            .mousedown(function (e) {
                e.preventDefault();
                spinner_hold_timeout = setInterval(function () {
                    step_numeric_input($(e.target));
                }, 150);
            })
            .bind('mouseup', function () {
                clearTimeout(spinner_hold_timeout);
            });


        $(window).on('beforeunload', save_form_data);
        form.find("button[name=clear]").click(clear_all);

        function int_css(element, property){
            return parseInt(element.css(property));
        }

        function activate_radio_input(input) {
            var radio = input.siblings(".radio");
            var name = input.attr("name");
            var group = radio_inputs.filter("[name=" + name + "]");

            group.each(function () {
                $(this).prop("checked", false);
            });

            input.prop("checked", true);
        }

        function clear_form_data_from_local_storage() {
            // clear all
            var starts_with = /^compyform/;
            Object.keys(localStorage).forEach(function (key) {
                if (starts_with.test(key)) {
                    localStorage.removeItem(key);
                }
            });
        }

        function set_input_label_as_valid(label){
            if (label.hasClass("invalid")) {
                // set label as valid and restore text
                label
                    .removeClass("invalid")
                    .text(label.data("text"));
            }
        }

        function toggle_checkbox_value(checkbox) {
            var input = checkbox.siblings("input");
            input.prop("checked", !input.prop("checked"));
        }

        function clear_all() {

            underlines
                .filter(".float-label")
                .removeClass("float-label");

            underline_inputs.val("");

            dropdowns.find(".placeholder").show();

            var invalid_labels = underline_labels.filter(".invalid");
            underline_labels.removeClass("invalid");


            setTimeout(function(){
                underline_labels.each(function(){
                    var label = $(this);
                    label.text(label.data("text"));
                });
            }, 250);

            checkbox_inputs
                .filter(":checked")
                .prop("checked", false);

            $('html, body').animate({
                scrollTop: form.offset().top - 10
            }, 'fast');
        }

        function hasAttr(element, attr) {
            return typeof element.attr(attr) !== "undefined";
        }

        function set_input_as_invalid(input){
            var label = input.siblings("label");
            if (!label.hasClass("invalid")) {

                // set error text to required if empty, error if set, otherwise invalid
                var error_text;
                if(hasAttr(input, "required") && input.val() === ""){
                    error_text = "Required"
                } else {
                    error_text = input.data("error") || "Invalid";
                }
                // set label as invalid, save text for later, and set text to error text
                label
                    .addClass("invalid")
                    .data("text", label.text())
                    .text(error_text);
            }
        }

        function validate_input(input){
            var value = input.val();
            if (hasAttr(input, "required") && value === "") {
                set_input_as_invalid(input);
            } else {
                var validation = input.data('validation');
                if (validation && !validate(validation, value)) {
                    set_input_as_invalid(input);
                }
            }
        }

        function validate_all() {
            inputs.each(function(){
                validate_input($(this));
            });
            return $(".invalid").size() === 0;
        }

        function step_numeric_input(arrow) {
            // assumed sanitized

            var input = arrow.siblings("input");
            var step = input.attr("step");


            if (arrow.hasClass("down")) {
                step *= -1;
            }

            var value = satitize_numeric_value(input.val());

            if (value === "") {
                value = step;
            } else {
                value = +(value) + +(step);
            }
            input.val(value);
            input.focus();
        }

        function satitize_numeric_value(value) {
            return value.replace(/[^0-9\+\-]/g, "");
        }

        function set_label_state(input, float_label) {
            if (float_label) {
                input.parent().addClass("float-label");
            } else {
                input.parent().removeClass("float-label");
            }
            // the 0.99 forces consistent AA on webkit
            // http://stackoverflow.com/a/11403025/828867
        }

        function check_label_state(input) {
            var not_empty = input.val().length > 0;
            var is_focus = input.is(":focus");
            set_label_state(input, not_empty || is_focus);
        }

        function is_valid_email_address(value) {
            var reg = /^([\w-]+\.?[\w-]*)+@([\w-]+\.[\w-]+)+$/;
            return reg.test(value);
        }

        function is_valid_number(value) {
            var reg = /^\d+$/;
            return reg.test(value);
        }

        function validate(validation, value) {
            return true;
            switch (validation) {
                case "email":
                    return is_valid_email_address(value);
                case "number":
                    return is_valid_number(value);
                case "password":
                    return value.length > 5;
                default:
                    return true;
            }
        }

        function save_form_data() {

            clear_form_data_from_local_storage();

            inputs.each(function () {
                var input = $(this);
                var type = input.attr("type");
                if (type === "password") {
                    // skip password
                    return true;
                }
                var name = input.attr("name");
                if (typeof name !== 'undefined') {

                    var storage_key = "compyform:" + type + ":" + name;
                    var value = input.val();
                    if (type === "checkbox" || type === "radio") {
                        if (input.is(":checked")) {
                            localStorage.setItem(storage_key, value);
                        }
                    } else {
                        if (value !== null && typeof value !== undefined && value.length > 0) {
                            localStorage.setItem(storage_key, value);
                        }
                    }
                }
            });
        }

        function load_form_data() {

            inputs.each(function () {
                var input = $(this);
                var type = input.attr("type");
                if (type === "password") {
                    // skip password
                    return true;
                }

                var name = input.attr("name");
                if (typeof name !== 'undefined') {
                    var value = localStorage.getItem("compyform:" + type + ":" + name);
                    if (value !== null && typeof value !== undefined) {
                        // set the field data here
                        if (type === "checkbox") {

                            input
                                .prop("checked", true)
                                .siblings(".checkbox")
                                .addClass("checked");

                        } else if (type === "radio") {
                            if (value === input.val()) {
                                activate_radio_input(input);
                            }
                        } else {
                            input.val(value);
                        }
                    }
                }
            });
        }
        //inputs.filter("[tabindex=1]").focus();
    }

    $(document).ready(setup_compy_form);
</script>

<style>
    .uploadButton {
        display:inline-block;
        position:relative;
        overflow:hidden;
        border-radius:3px;
        background:#099;
        color:#fff;
        text-align:center;
        padding:10px;
        line-height:30px;
        width:180px;
        cursor:pointer;
    }
    .uploadButton:hover {
        background:#0aa;
    }
    .uploadButton input[type=file] {
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:100%;
        cursor:pointer;
        opacity:0;
    }
    .uploadValue {
        display:none;
        background:rgba(255,255,255,0.2);
        border-radius:3px;
        padding:3px;
        color:#ffffff;
    }

    /* COLORS */
    /**** FORM ****/
    /* GENERAL */
    /* LABELS */
    /* HEADINGS */
    /* INPUTS */
    /* SELECT -- DROPDOWNS */
    /* PLACEHOLDERS */
    /* TEXTAREA */
    /* BUTTONS */
    /* RADIO BUTTONS */
    /* CHECKBOXES */
    /* UNDERLINE */
    /* SPINNER ARROWS */
    /* FIELD CONTROLS */
    body {
        background-color: #fff;
        height: 100%;
    }
    form {
        padding: 40px 0 80px 0;
    }
    .form-inner {
        zoom: 1;
        margin: auto;
        padding: 10px 5px 10px 5px;
        width: 100%;
        max-width: 600px;
        box-sizing: border-box;
    }
    .form-inner:before,
    .form-inner:after {
        content: "";
        display: table;
    }
    .form-inner:after {
        clear: both;
    }
    .form-inner > h2 {
        font-size: 28px;
        font-family: "Roboto", sans-serif;
        color: #777;
        margin: 0;
        margin-top: 50px !important;
        margin-bottom: 10px !important;
        margin-left: 10px;
        font-weight: bold;
        display: block;
        position: relative;
        float: left;
        width: 90%;
    }
    .form-inner > div {
        padding-bottom: 0;
        padding-top: 26px;
        width: 100%;
        float: left;
        position: relative;
    }
    .form-inner > div.half {
        width: 50%;
    }
    .form-inner > div.quarter {
        width: 25%;
    }
    @media all and (max-width: 360px) {
        .form-inner > div.half {
            width: 100%;
        }
        .form-inner > div.quarter {
            width: 50%;
        }
    }
    .form-inner > div.align-right {
        float: right;
    }
    .form-inner > div.no-label {
        padding-top: 15px;
    }
    .form-inner > div > select {
        position: absolute;
        opacity: 0;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 500;
    }
    .form-inner > div.dropdown {
        cursor: pointer;
    }
    .form-inner > div.dropdown input {
        cursor: pointer;
    }
    .form-inner > div.dropdown label {
        cursor: pointer !important;
    }
    .form-inner > div.dropdown .dropdown-arrow {
        bottom: 6px;
        padding: 12px;
    }
    .form-inner > div.dropdown .dropdown-arrow > div {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border-top: 7px solid #bbb;
        border-left: 7px solid transparent;
        border-right: 7px solid transparent;
    }
    .form-inner > div.dropdown .dropdown-arrow:active > div {
        border-top: 7px solid #1065e7;
    }
    .form-inner > div.dropdown.open .dropdown-arrow > div {
        -webkit-transform: rotate(180deg);
        transform: rotate(180deg);
    }
    .form-inner > div.dropdown.open .underline:before,
    .form-inner > div.dropdown.open .underline::before,
    .form-inner > div.dropdown.open .underline:after,
    .form-inner > div.dropdown.open .underline::after {
        background-color: #1065e7;
        height: 35px;
    }
    .form-inner > div.dropdown.open .underline label {
        color: #1065e7 !important;
    }
    .form-inner > div.dropdown.open .underline.float-label:before,
    .form-inner > div.dropdown.open .underline.float-label::before {
        height: 45px;
    }
    .form-inner > div.dropdown.open .dropdown-box {
        padding: 8px;
        padding-top: 0;
        height: auto;
        border: 2px solid #1065e7;
        border-top: none;
    }
    .form-inner > div.dropdown .dropdown-box {
        background-color: #fff;
        border-bottom: 0 solid transparent;
        border-left: 2px solid #1065e7;
        border-right: 2px solid #1065e7;
        border-top: none;
        width: 100%;
        max-width: 100%;
        height: 0;
        top: 100%;
        box-sizing: border-box;
        overflow: hidden;
        z-index: 1000;
        position: absolute;
        left: 0;
    }
    .form-inner > div.dropdown .dropdown-box > .focus-helper {
        position: absolute;
        opacity: 0;
        top: 0;
        left: 0;
        width: 0;
        height: 0;
        font-size: 0;
        margin: 0;
        padding: 0;
        border: none;
    }
    .form-inner > div.dropdown .dropdown-box > div {
        max-height: 150px;
        overflow: auto;
    }
    .form-inner > div.dropdown .dropdown-box > div > div {
        width: 100%;
        height: 30px;
    }
    .form-inner > div.dropdown .dropdown-box > div > div > p {
        cursor: pointer;
        display: block;
        line-height: 30px;
        font-family: "Roboto", sans-serif;
        font-size: 18px;
        color: #333;
        font-weight: normal;
    }
    .form-inner > div.buttons {
        margin-top: 10px;
    }
    .form-inner > div.buttons > div {
        width: 25%;
        padding: 0 10px;
        box-sizing: border-box;
        margin: 0;
        float: left;
    }
    .form-inner > div.buttons > div > button {
        font-family: "Roboto", sans-serif;
        font-weight: bold;
        border: none;
        outline: none;
        background-color: none;
        background: none;
        border: 2px solid #777;
        padding: 5px 0;
        font-size: 12px;
        width: 100%;
        cursor: pointer;
        color: #111;
    }
    .form-inner > div.buttons > div > button:focus {
        border-color: #1065e7;
        color: #333;
    }
    .form-inner > div.buttons > div > button:active {
        background-color: #1065e7;
        color: #fff !important;
    }
    .form-inner > div > div {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        position: relative;
        margin: 0 10px;
    }
    .form-inner > div > div.prefixed > input {
        padding-left: 15px;
    }
    .form-inner > div > div.right-control {
        padding-right: 30px !important;
    }
    .form-inner > div > div.underline {
        border-bottom: 2px solid #bbb;
        padding: 0 8px 6px 8px;
    }
    .form-inner > div > div.underline:before,
    .form-inner > div > div.underline::before,
    .form-inner > div > div.underline:after,
    .form-inner > div > div.underline::after {
        content: "";
        height: 5px;
        width: 2px;
        position: absolute;
        bottom: 0;
        background-color: #bbb;
    }
    .form-inner > div > div.underline:before,
    .form-inner > div > div.underline::before {
        left: 0;
    }
    .form-inner > div > div.underline:after,
    .form-inner > div > div.underline::after {
        right: 0;
    }
    .form-inner > div > div.underline.dropdown-hint:after,
    .form-inner > div > div.underline.dropdown-hint::after {
        width: 0;
        height: 0;
        background-color: transparent;
        border-right: 5px solid #bbb;
        border-bottom: 5px solid #bbb;
        border-left: 5px solid transparent;
        border-top: 5px solid transparent;
    }
    .form-inner > div > div.underline.focused {
        border-color: #1065e7;
    }
    .form-inner > div > div.underline.focused:before,
    .form-inner > div > div.underline.focused::before,
    .form-inner > div > div.underline.focused:after,
    .form-inner > div > div.underline.focused::after {
        background-color: #1065e7;
    }
    .form-inner > div > div.float-label > .placeholder {
        opacity: 0 !important;
    }
    .form-inner > div > div.float-label > label,
    .form-inner > div > div.float-label .prefix {
        opacity: 0.99 !important;
    }
    .form-inner > div > div > textarea {
        border: none;
        color: #111;
        width: 100%;
        resize: none;
        outline: none;
        padding: 0;
        margin: 0;
        -webkit-appearance: none;
        border-radius: 0;
        overflow: hidden;
        border-image-width: 0;
        font-family: "Roboto", sans-serif;
        font-size: 18px;
        font-weight: normal;
        position: relative;
        z-index: 1;
        background-color: transparent;
        display: block;
    }
    .form-inner > div > div > textarea:focus ~ label {
        color: #1065e7;
    }
    .form-inner > div > div > textarea ~ label {
        cursor: text;
    }
    .form-inner > div > div > input {
        position: relative;
        border: none;
        border-image-width: 0;
        outline: none;
        margin: 0;
        font-size: 18px;
        padding: 0;
        z-index: 1;
        -webkit-appearance: none;
        border-radius: 0;
        background-color: transparent;
        width: 100%;
        display: block;
        box-sizing: border-box;
        color: #111;
        font-family: "Roboto", sans-serif;
        font-weight: bold;
    }
    .form-inner > div > div > input ~ label {
        cursor: text;
    }
    .form-inner > div > div > input[type=checkbox],
    .form-inner > div > div > input[type=radio] {
        display: none;
    }
    .form-inner > div > div > input[type=checkbox] ~ label,
    .form-inner > div > div > input[type=radio] ~ label {
        opacity: 1 !important;
        cursor: pointer;
        position: absolute;
        font-size: 18px;
        font-weight: normal;
        color: #777;
        font-family: "Roboto", sans-serif;
        top: 0px;
        left: 0;
        box-sizing: border-box;
        width: auto;
        padding-left: 52px;
        line-height: 32px;
    }
    .form-inner > div > div > input[type=checkbox]:checked ~ label,
    .form-inner > div > div > input[type=radio]:checked ~ label {
        color: #333;
    }
    .form-inner > div > div > input[type=checkbox]:checked ~ .checkbox,
    .form-inner > div > div > input[type=radio]:checked ~ .checkbox {
        border-color: #333;
    }
    .form-inner > div > div > input[type=checkbox]:checked ~ .checkbox > span,
    .form-inner > div > div > input[type=radio]:checked ~ .checkbox > span {
        border-color: #333;
    }
    .form-inner > div > div > input[type=checkbox]:checked ~ .radio,
    .form-inner > div > div > input[type=radio]:checked ~ .radio {
        border-color: #333;
    }
    .form-inner > div > div > input[type=checkbox]:checked ~ .radio > span,
    .form-inner > div > div > input[type=radio]:checked ~ .radio > span {
        background-color: #333;
    }
    .form-inner > div > div > input:focus ~ label {
        color: #1065e7;
    }
    .form-inner > div > div > label {
        width: 100%;
        padding-bottom: 2px;
        cursor: default;
        font-weight: bold;
        position: absolute;
        font-size: 12px;
        font-family: "Roboto", sans-serif;
        top: -14px;
        left: 8px;
        opacity: 0;
        color: #777;
    }
    .form-inner > div > div > label.invalid {
        color: #d4412c;
        opacity: 1;
    }
    .form-inner > div > div > .dropdown-arrow,
    .form-inner > div > div .spinner-arrow {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        position: absolute;
        right: 0;
        z-index: 6;
        cursor: pointer;
    }
    .form-inner > div > div > .spinner-arrow {
        padding: 0px 12px;
    }
    .form-inner > div > div > .spinner-arrow.up {
        bottom: 25px;
        padding-top: 12px;
        padding-bottom: 6px;
    }
    .form-inner > div > div > .spinner-arrow.up > div {
        border-bottom: 7px solid #bbb;
    }
    .form-inner > div > div > .spinner-arrow.up:active > div {
        border-bottom: 7px solid #1065e7;
    }
    .form-inner > div > div > .spinner-arrow.down {
        padding-bottom: 12px;
        padding-top: 6px;
        bottom: 0;
    }
    .form-inner > div > div > .spinner-arrow.down > div {
        border-top: 7px solid #bbb;
    }
    .form-inner > div > div > .spinner-arrow.down:active > div {
        border-top: 7px solid #1065e7;
    }
    .form-inner > div > div > .spinner-arrow > div {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border-left: 7px solid transparent;
        border-right: 7px solid transparent;
    }
    .form-inner > div > div > span.placeholder {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        position: absolute;
        left: 8px;
        bottom: 8px;
        color: #777;
        font-family: "Roboto", sans-serif;
        font-weight: normal;
        font-size: 18px;
        z-index: 0;
        opacity: 0.99;
    }
    .form-inner > div > div > span.prefix {
        font-size: 18px;
        font-family: "Roboto", sans-serif;
        position: absolute;
        color: #777;
        left: 8px;
        bottom: 8px;
        opacity: 0;
    }
    .form-inner > div > div > span.checkbox {
        width: 32px;
        height: 32px;
        border: 2px solid #bbb;
        display: block;
        position: relative;
        cursor: pointer;
        outline: none;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        box-sizing: border-box;
    }
    .form-inner > div > div > span.checkbox > span {
        border-right: 2px solid transparent;
        border-bottom: 2px solid transparent;
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);
        width: 6px;
        height: 14px;
        position: relative;
        display: block;
        top: 4.17157288px;
        left: 10px;
    }
    .form-inner > div > div > span.radio {
        width: 32px;
        height: 32px;
        border: 2px solid #bbb;
        display: block;
        position: relative;
        cursor: pointer;
        outline: none;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        border-radius: 50%;
        box-sizing: border-box;
    }
    .form-inner > div > div > span.radio > span {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: transparent;
        position: relative;
        display: block;
        top: 6px;
        left: 6px;
    }
    .form-inner > div > div > span.radio.checked {
        border-color: #333 !important;
    }
    .form-inner > div > div > span.radio.checked > span {
        background-color: #333 !important;
    }
    html.no-touch form *,
    html.no-touch form :before,
    html.no-touch form ::before,
    html.no-touch form :after,
    html.no-touch form ::after {
        transition: opacity 0.08s ease-out, background 0.08s ease-out, color 0.08s ease-out, border 0.08s ease-out, max-height 0.08s ease-out, height 0.08s ease-out, width 0.08s ease-out;
    }
    html.no-touch form .checkbox:hover,
    html.no-touch form .radio:hover {
        border-color: #777;
    }
    html.no-touch form .checkbox:hover > span {
        border-color: #bbb;
    }
    html.no-touch form .checkbox:active,
    html.no-touch form .checkbox:focus {
        border-color: #1065e7 !important;
    }
    html.no-touch form .checkbox:focus ~ label {
        color: #1065e7 !important;
    }
    html.no-touch form .radio:active,
    html.no-touch form .radio:focus {
        border-color: #1065e7 !important;
    }
    html.no-touch form .radio:hover > span {
        background-color: #bbb;
    }
    html.no-touch form .input-hover {
        border-color: #777;
    }
    html.no-touch form .input-hover:before,
    html.no-touch form .input-hover::before,
    html.no-touch form .input-hover:after,
    html.no-touch form .input-hover::after {
        background-color: #777;
    }
    html.no-touch form .input-hover .spinner-arrow.up > div {
        border-bottom: 7px solid #777;
    }
    html.no-touch form .input-hover .spinner-arrow.down > div {
        border-top: 7px solid #777;
    }
    html.no-touch form .input-hover .dropdown-arrow > div {
        border-top: 7px solid #777;
    }
    html.no-touch form .buttons > div > button:hover {
        border-color: #1065e7;
        color: #333;
    }
    html.no-touch form .spinner-arrow:hover.up > div {
        border-bottom: 7px solid #777;
    }
    html.no-touch form .spinner-arrow:hover.down > div {
        border-top: 7px solid #777;
    }
    html.no-touch form .dropdown-box p:hover {
        color: #1065e7 !important;
    }
    html.no-touch form .dropdown:hover .dropdown-arrow > div {
        border-top: 7px solid #777;
    }
    html.no-touch form input[type=checkbox] ~ label:hover ~ span.checkbox,
    html.no-touch form input[type=radio] ~ label:hover ~ span.checkbox {
        border-color: #777;
    }
    html.no-touch form input[type=checkbox] ~ label:hover ~ span.checkbox > span,
    html.no-touch form input[type=radio] ~ label:hover ~ span.checkbox > span {
        border-color: #bbb;
    }
    html.no-touch form input[type=checkbox] ~ label:hover ~ span.radio,
    html.no-touch form input[type=radio] ~ label:hover ~ span.radio {
        border-color: #777;
    }
    html.no-touch form input[type=checkbox] ~ label:hover ~ span.radio > span,
    html.no-touch form input[type=radio] ~ label:hover ~ span.radio > span {
        background-color: #bbb;
    }
    html.no-touch form input[type=checkbox]:checked ~ label:hover ~ span.checkbox,
    html.no-touch form input[type=radio]:checked ~ label:hover ~ span.checkbox {
        border-color: #333;
    }
    html.no-touch form input[type=checkbox]:checked ~ label:hover ~ span.checkbox > span,
    html.no-touch form input[type=radio]:checked ~ label:hover ~ span.checkbox > span {
        border-color: #333;
    }
    html.no-touch form input[type=checkbox]:checked ~ label:hover ~ span.radio,
    html.no-touch form input[type=radio]:checked ~ label:hover ~ span.radio {
        border-color: #333;
    }
    html.no-touch form input[type=checkbox]:checked ~ label:hover ~ span.radio > span,
    html.no-touch form input[type=radio]:checked ~ label:hover ~ span.radio > span {
        background-color: #333;
    }
    .clearfix {
        zoom: 1;
    }
    .clearfix:before,
    .clearfix:after {
        content: "";
        display: table;
    }
    .clearfix:after {
        clear: both;
    }
    .unselectable {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    ::-ms-clear {
        display: none;
    }
    * {
        -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
    }
    input::-moz-focus-inner {
        padding: 0 !important;
        border: 0 none !important;
    }
    .animate {
        transition: opacity 0.08s ease-out, background 0.08s ease-out, color 0.08s ease-out, border 0.08s ease-out, max-height 0.08s ease-out, height 0.08s ease-out, width 0.08s ease-out;
    }

</style>