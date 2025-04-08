<?php

ini_set('memory_limit', '300M');
set_time_limit(0);

$months = ["január", "február", "március", "április", "május", "június", "július", "augusztus", "szeptember", "október", "november", "december"];
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   
   
    <title>Projects</title>

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            line-height: 1.5;
        }

        .ml-10 {
            margin-left: 10px;
        }

        .center {
            margin: auto;
            width: 90%;
            padding: 10px;
        }

        @media screen and (max-width: 992px) {
            .center {
                margin: auto;
                width: 80%;
                padding: 10px;
            }
        }

        @media screen and (max-width: 600px) {
            .center {
                margin: auto;
                width: 90%;
                padding: 10px;
            }

            body {
                text-align: center;
            }
        }

        .heading {
            background-color: rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .main {
            margin-top: 20px;
            background-color: rgba(0, 0, 0, 0.05);
            padding: 20px;
        }

        .form-group {
            display: -ms-flexbox;
            display: flex;
            -ms-flex: 0 0 auto;
            flex: 0 0 auto;
            -ms-flex-flow: row wrap;
            flex-flow: row wrap;
            -ms-flex-align: center;
            align-items: center;
            margin-bottom: 0
        }

        .input-style {
            width: 100%;
            padding: 10px 0px;
            font-size: 1rem;
            line-height: 1.25;
            color: #495057;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.15);
            margin-top: 5px;
            display: block;
            font-weight: 400;
            background-clip: padding-box;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .input-style:focus {
            color: #495057;
            background-color: #fff;
            border-color: #3870ff;
            outline: none;
        }

        .success {
            background-color: lightgreen;
            color: darkgreen;
            padding: 15px;
            width: 98%;
        }

        .label-style {
            font-size: 15px;
        }

        .row {
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .row-2 {
            padding-top: 5px;
            padding-bottom: 5px;
        }

        .add-new-btn {
            background-color: #80bdff;
            color: #fff;
            border: 1px solid #3870ff;
            padding: 10px 50px;
        }

        .add-new-btn:focus {
            outline: none;
        }

        .add-new-btn:hover {
            background-color: #2c3e50;
        }

        .delete-btn {
            background-color: #ff3636;
            color: #fff;
            border: 1px solid #ed0000;
            padding: 10px 50px;
        }

        .delete-btn:focus {
            outline: none;
        }

        .delete-btn:hover {
            background-color: #ed0000;
        }

        .center-heading {
            display: flex;
            justify-content: center;
        }

        .save-btn {
            background-color: #b3b3b3;
            color: #fff;
            border: 1px solid #808080;
            padding: 10px 50px;
        }

        .save-btn:focus {
            outline: none;
        }

        .save-btn:hover {
            background-color: #2c3e50;
        }

        .text-center {
            text-align: center !important;
        }

        .users {
            padding: 10px 30px;
            border: 1px solid;
            margin: 5px 5px;
            cursor: pointer;
        }

        .users:hover {
            background-color: rgba(0, 0, 0, 0.2);
        }

        .d-none {
            display: none !important;
            transition-timing-function: ease-in-out;
        }

        .clock {
            background-color: rgba(0, 0, 0, 0.05);

            padding: 10px;
        }

        .m-t-20 {
            margin-top: 20px;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .w-100 {
            width: 95%;
            padding: 0 10px;
        }

        .w-45 {
            width: 40%;
            padding: 0 10px;
        }

        .w-30 {
            width: 30%;
            padding: 0 10px;
        }

        .w-15 {
            width: 15%;
            padding: 0 10px;
        }

        .w-5 {
            width: 10%;
            padding: 0 10px;
        }

        @media screen and (max-width: 1805px) {
            .w-45 {
                width: 39%;
            }

            .success {
                width: 97%;
            }
        }

        @media screen and (max-width: 1510px) {
            .w-45 {
                width: 38%;
            }

            .success {
                width: 97%;
            }
        }

        @media screen and (max-width: 1300px) {
            .w-45 {
                width: 37%;
            }

            .success {
                width: 96%;
            }
        }

        @media screen and (max-width: 1140px) {
            .w-45 {
                width: 36%;
            }

            .success {
                width: 96%;
            }
        }

        @media screen and (max-width: 1015px) {
            .w-45 {
                width: 35%;
            }

            .success {
                width: 96%;
            }
        }


        /* tablets */
        @media screen and (max-width: 992px) {
            .w-45 {
                width: 95%;
            }

            .w-30 {
                width: 95%;
                padding: 0 10px;
            }

            .w-15 {
                width: 46.2%;
                padding: 0 10px;
            }

            .w-5 {
                width: 46.2%;
                padding: 0 10px;
            }
        }

        @media screen and (max-width: 800px) {
            .w-45 {
                width: 95%;
            }

            .w-30 {
                width: 95%;
                padding: 0 10px;
            }

            .w-15 {
                width: 46%;
                padding: 0 10px;
            }

            .w-5 {
                width: 46%;
                padding: 0 10px;
            }
        }

        @media screen and (max-width: 780px) {
            .w-45 {
                width: 95%;
            }

            .w-30 {
                width: 95%;
                padding: 0 10px;
            }

            .w-15 {
                width: 45.8%;
                padding: 0 10px;
            }

            .w-5 {
                width: 45.8%;
                padding: 0 10px;
            }

            .success {
                width: 90%;
            }
        }

        @media screen and (max-width: 625px) {
            .w-45 {
                width: 95%;
            }

            .w-30 {
                width: 95%;
                padding: 0 10px;
            }

            .w-15 {
                width: 95%;
                padding: 0 10px;
            }

            .w-5 {
                width: 95%;
                padding: 0 10px;
            }

            .add-new-btn {
                width: 95%;
            }

            .save-btn {
                width: 95%;
            }

            .delete-btn {
                margin-top: 10px;
                width: 95%;
            }
        }



        .row {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px
        }

        /* Removes the clear button from date inputs */
        input[type="date"]::-webkit-clear-button {
            display: none;
        }

        /* Removes the spin button */
        input[type="date"]::-webkit-inner-spin-button {
            display: none;
        }

        /* Always display the drop down caret */
        input[type="date"]::-webkit-calendar-picker-indicator {
            color: #2c3e50;
        }

        /* A few custom styles for date inputs */
        input[type="date"] {
            appearance: none;
            -webkit-appearance: none;
            font-size: 15px;
            border: 1px solid rgba(0, 0, 0, 0.15);
            padding: 10px 0;
            display: inline-block !important;
            visibility: visible !important;
        }

        input[type="date"],
        focus {
            box-shadow: none;
            -webkit-box-shadow: none;
            -moz-box-shadow: none;
        }

        input[type="time"] {
            -webkit-appearance: none;
            font-size: 15px;
            border: 1px solid rgba(0, 0, 0, 0.15);
            padding: 10px 0;
            display: inline-block !important;
            visibility: visible !important;
        }

        .display-table td,
        .display-table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .display-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .display-table tr:hover {
            background-color: #ddd;
        }

        .display-table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4CAF50;
            color: white;
        }

        .row.mitnem {
            display: none;
        }
    </style>
</head>

<body>
    <div class="center">
        <div class="clock text-center d-none" id="sumtime">
            <b><span>00:00</span></b>
        </div>
        <div style="clear:both;"></div>
        <div class="main" id="heading">
            <h4 class="text-center">Ki vagy?</h4>
            <hr>
            <div class="row text-center center-heading" id="users">
            </div>
        </div>

        <div class="main d-none" id="content">
            <input type="hidden" value="" id="name">
            <div class="error-message hidden"></div>
            <div class="row-2 d-none" id="success">
                <div class="success">Sikeres mentés!</div>
                <br>
                <h4>Eddig felvett feladatok:</h4>
                <table class="display-table hidden">
                    <tr>
                        <th>Dátum</th>
                        <th>Név</th>
                        <th>Feladat</th>
                    </tr>
                </table>
            </div>
            <div class="row" id="main-field">
                <div class="w-45">
                    <input type="text" class="input-style" name="feladat" required autocomplete="off"
                        placeholder=" Írjon be egy feladatot...">
                </div>
                <div class="w-15">
                    <input type="date" class="input-style" name="datum" required autocomplete="off"
                        value='<?php echo date('Y-m-d') ?>'>
                </div>
                <div class="w-5">
                    <input type="time" class="input-style" name="ido" required
                        autocomplete="off">
                </div>
                <div class="w-26">
                    <select name="koltseghely" id="koltseghely" class="input-style" required autocomplete="off"
                        onchange="addPlusField(this,this.value,1)">
                    </select>
                </div>
                <label style="margin:0px 3px;">
                    Túlóra
                    <input type="checkbox" name="tulora" id="tulora" class="input-style" required autocomplete="off">

                </label>
            </div>

            <div class="row mitnem" id="add-plus-1" name="mitnem" style="margin-left: 0"></div>
            <div id="new_project"></div>

            <div class="row">
                <button id="add_new_project" class="add-new-btn ml-10" onclick="addNewProject()" type="button">+ Új
                    feladat hozzáadása
                </button>
                <button id="delete_project" class="delete-btn ml-10 d-none" onclick="deleteProject()" type="button">-
                    Feladat
                    törlése
                </button>
            </div>
            <div class="row">
                <button id="save" class="save-btn ml-10" type="button">Mentés
                </button>
            </div>
        </div>

        <div style="clear:both;"></div>
    </div>

    <script type="text/javascript" src="js/main.js?<?php echo time(); ?>"></script>
</body>

</html>