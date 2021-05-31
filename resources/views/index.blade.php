<!doctype html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://use.fontawesome.com/98f46c2ed4.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">Team</th>
                    <th scope="col">Played</th>
                    <th scope="col">Won</th>
                    <th scope="col">Drawn</th>
                    <th scope="col">Lost</th>
                    <th scope="col">GF</th>
                    <th scope="col">GA</th>
                    <th scope="col">GD</th>
                    <th scope="col">Points</th>
                </tr>
                </thead>
                <tbody id="teams">
                </tbody>
            </table>
        </div>
        <div class="col-md-5" id="fixtureList">
        </div>
        <div class="col-md-6">
            <ul class="list-group row" style="border: 1px solid #ccc;padding-bottom: 10px">
                <li class="list-group-item active col-md-12" aria-current="true" style="margin-bottom: 5px"> Result</li>
                <div class="row" id="resultMatch">
                    <div class="alert alert-primary" role="alert">Herhangi Bir Maç Bulunmamaktadır !</div>
                </div>
            </ul>
        </div>

        <div class="col-md-1">
            <button type="submit" class="btn btn-success form-control oyna"><i class="fa fa-play-circle"
                                                                               aria-hidden="true"></i></button>
            <br><br>
            <button type="submit" class="btn btn-warning form-control fiksturolustur"><i class="fa fa-refresh"
                                                                                         aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
        integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
        integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
        crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script>


    $(function () {

        $(document).ready(function () {
            $.ajax({
                type: 'POST',
                url: '{{url('/clearFixture')}}',
                success: function (result) {
                    $("#resultMatch").empty();
                    $("#resultMatch").html('<div class="alert alert-primary" role="alert">Herhangi Bir Maç Bulunmamaktadır !</div>');
                    $.ajax({
                        type: 'POST',
                        url: '{{url('/createFixture')}}',
                        dataType: "json",
                        success: function (result) {
                            $("#fixtureList").html(result);
                            $.ajax({
                                type: 'POST',
                                url: '{{url('/createFixture')}}',
                                dataType: "json",
                                success: function (result) {
                                    $("#fixtureList").html(result);

                                    puanguncelle();

                                    $.ajax({
                                        type: 'POST',
                                        url: '{{url('/getMatchScore')}}',
                                        dataType: "json",
                                        success: function (result) {
                                            var text = "";
                                            round = 1;
                                            $.each(result, function (i, item) {
                                                if (round != this.round) {
                                                    text += '<div class="col-md-12"><hr></div>';
                                                    round = this.round;
                                                }
                                                text += '<div class="col-md-6" style="text-align: right !important;">';
                                                text += this.home_team_name + '  <span class="badge badge-secondary" style="background-color: red !important;">' + this.home_team_score + '</span>';
                                                text += " </div> ";
                                                text += '<div class="col-md-6" style="text-align: left">';
                                                text += '<span class="badge badge-secondary" style="background-color: red !important;">  ' + this.away_team_score + '</span>' + this.away_team_name;
                                                text += " </div> ";
                                            });
                                            if (text == "") {
                                                $("#resultMatch").html('<div class="alert alert-primary" role="alert">Herhangi Bir Maç Bulunmamaktadır !</div>');
                                            } else {
                                                $("#resultMatch").html(text);
                                            }

                                        }
                                    });

                                }
                            });
                        }
                    });
                    puanguncelle();
                }
            });


        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function puanguncelle() {
            $.ajax({
                type: 'POST',
                url: '{{url('/getPointsTable')}}',
                dataType: "json",
                success: function (result) {
                    var text = "";
                    $.each(result, function (i, item) {
                        text += "<tr>";
                        text += "<td>" + this.team.team_name + "</td>";
                        text += "<td>" + this.play + "</td>";
                        text += "<td>" + this.win + "</td>";
                        text += "<td>" + this.draw + "</td>";
                        text += "<td>" + this.lost + "</td>";
                        text += "<td>" + this.gf + "</td>";
                        text += "<td>" + this.ga + "</td>";
                        text += "<td>" + this.gd + "</td>";
                        text += "<td>" + this.pts + "</td>";
                        text += "</tr>";
                    });
                    $("#teams").html(text);
                }
            });
        }

        $('.puanguncelle').on('click', function () {
            $.ajax({
                type: 'POST',
                url: '{{url('/getPointsTable')}}',
                dataType: "json",
                success: function (result) {
                    var text = "";
                    $.each(result, function (i, item) {
                        text += "<tr>";
                        text += "<td>" + this.team.team_name + "</td>";
                        text += "<td>" + this.play + "</td>";
                        text += "<td>" + this.win + "</td>";
                        text += "<td>" + this.draw + "</td>";
                        text += "<td>" + this.lost + "</td>";
                        text += "<td>" + this.gf + "</td>";
                        text += "<td>" + this.ga + "</td>";
                        text += "<td>" + this.gd + "</td>";
                        text += "<td>" + this.pts + "</td>";
                        text += "</tr>";
                    });
                    $("#teams").html(text);
                }
            });
        });

        $('.oyna').on('click', function () {
            $.ajax({
                type: 'POST',
                url: '{{url('/playMatch')}}',
                dataType: "json",
                success: function (result) {
                    var text = "";
                    var round = 1;
                    $.each(result, function (i, item) {
                        if (round != this.round) {
                            text += '<div class="col-md-12"><hr></div>';
                            round = this.round;
                        }
                        text += '<div class="col-md-6" style="text-align: right !important;">';
                        text += this.home_team_name + '<span class="badge badge-secondary" style="background-color: red !important;">' + this.home_team_score + '</span>';
                        text += " </div> ";
                        text += '<div class="col-md-6" style="text-align: left">';
                        text += '<span class="badge badge-secondary" style="background-color: red !important;margin-right: 3px; !important;">' + this.away_team_score + '</span> &nbsp;&nbsp;' + this.away_team_name;
                        text += " </div> ";


                    });
                    $("#resultMatch").html(text);
                }
            });

            puanguncelle();
        });

        $('.fiksturolustur').on('click', function () {

            $.ajax({
                type: 'POST',
                url: '{{url('/clearFixture')}}',
                success: function (result) {
                    $("#resultMatch").empty();
                    $("#resultMatch").html('<div class="alert alert-primary" role="alert">Herhangi Bir Maç Bulunmamaktadır !</div>');
                    $.ajax({
                        type: 'POST',
                        url: '{{url('/createFixture')}}',
                        dataType: "json",
                        success: function (result) {
                            $("#fixtureList").html(result);
                        }
                    });
                    puanguncelle();
                }
            });

        });
    });
</script>
</body>
</html>


