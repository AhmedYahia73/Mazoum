<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 portrait; margin: 0; }
body { margin: 0; padding: 0; }
.bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 210mm;
    height: 297mm;
    z-index: -1;
}
.btns {
    position: fixed;
    bottom: 25mm;
    left: 0;
    width: 100%;
    text-align: center;
}
.a1 {
    display: inline;
    padding-top: 3mm;
    padding-bottom: 3mm;
    padding-left: 7mm;
    padding-right: 7mm;
    font-size: 13pt;
    font-weight: bold;
    font-family: Arial;
    text-decoration: none;
    color: #ffffff;
    background-color: #1e6b40;
}
.a2 {
    display: inline;
    padding-top: 3mm;
    padding-bottom: 3mm;
    padding-left: 7mm;
    padding-right: 7mm;
    font-size: 13pt;
    font-weight: bold;
    font-family: Arial;
    text-decoration: none;
    color: #ffffff;
    background-color: #8e2020;
}
.spacer {
    display: inline-block;
    width: 10mm;
}
</style>
</head>
<body>
<img src="{{ $imageBase64 }}" class="bg" />
<div class="btns">
    <a href="{{ $confirm_link }}" class="a1">تأكيد الحضور</a>
    <span class="spacer"></span>
    <a href="{{ $apologize_link }}" class="a2">الاعتذار عن الحضور</a>
</div>
</body>
</html>