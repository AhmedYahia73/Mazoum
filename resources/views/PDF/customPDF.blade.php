<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { size: A4 portrait; margin: 0; }
body { margin: 0; padding: 0; }
.wrap { position: relative; width: 210mm; height: 297mm; }
.bg { position: absolute; top: 0; left: 0; width: 210mm; height: 297mm; }
.btns { position: absolute; bottom: 25mm; left: 0; width: 100%; text-align: center; }
.tbl { margin: 0 auto; }
.td1 { padding-right: 6mm; }
.td2 { padding-left: 6mm; }
.a1 { display: block; padding-top: 4mm; padding-bottom: 4mm; padding-left: 8mm; padding-right: 8mm; font-size: 14pt; font-weight: bold; font-family: Arial; text-decoration: none; color: #ffffff; background-color: #1e6b40; }
.a2 { display: block; padding-top: 4mm; padding-bottom: 4mm; padding-left: 8mm; padding-right: 8mm; font-size: 14pt; font-weight: bold; font-family: Arial; text-decoration: none; color: #ffffff; background-color: #8e2020; }
</style>
</head>
<body>
<div class="wrap">
<img src="{{ $imageBase64 }}" class="bg" />
<div class="btns">
<table class="tbl" cellpadding="0" cellspacing="0">
<tr>
<td class="td1"><a href="{{ $confirm_link }}" class="a1">تأكيد الحضور</a></td>
<td class="td2"><a href="{{ $apologize_link }}" class="a2">الاعتذار عن الحضور</a></td>
</tr>
</table>
</div>
</div>
</body>
</html>