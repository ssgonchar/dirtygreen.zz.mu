<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8">
    <meta name="lang" content="eng">
    <meta name="title" content="{$smarty.const.APP_NAME}">
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <title>{$smarty.const.APP_NAME}{if (!empty($page_title))} - {$page_title}{/if}</title>
    <link rel="icon" href="/favicon.ico" type="image/icon.ico">
</head>
{literal}
<style type="text/css">
    /*---- RESET RULES ----*/
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, font, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td { margin: 0; padding: 0; border: 0; outline: 0; font-size: 100%; vertical-align: baseline; background: transparent; }

body { line-height: 1; width: 100%; height: 100%; font-family:  Tahoma, Arial, Verdana, Helvetica, sans-serif; font-size: 12px; }
ol, ul { list-style: none; }
blockquote, q { quotes: none; }

blockquote:before, blockquote:after,
q:before, q:after { content: ''; content: none; }

/* remember to define focus styles! */
:focus { outline: 0; }

/* remember to highlight inserts somehow! */
ins { text-decoration: none; }
del { text-decoration: line-through; }

/* tables still need 'cellspacing="0"' in the markup */
table { border-collapse: collapse; border-spacing: 0; }
/*---- END OF RESET RULES ----*/

.content-main-container { width: 1024px; padding: 15px; }
.cmc-page-name { position: relative; width: 100%; height: 52px; font-family: Verdana, sans-serif !important; }
.cmc-page-name h1 { text-align: center; padding: 15px 0 0 !important; margin: 0 !important; font: bold 18px/18px Verdana, sans-serif; }

.separator { clear: both; }
.pad { height: 20px; }
.pad1 { height: 10px; }
.pad2 { height: 40px; }

table { font-size: 12px; }
table tr td, table tr th { vertical-align: middle; font-family: Tahoma, Arial, Verdana, Helvetica, sans-serif; font-size: 12px; }

table.form tr td { padding: 5px 10px 5px 0; }
table.form a {color: #000000; font-weight:100 } 
table.form tr.deleted td, table.form tr.deleted td input, table.form tr.deleted td select { color: #000000; border-color: #000000; }
table.form tr.deleted td a { color: #000000aaa; }

table.list {margin-bottom:10px; clear: both; border: 1px solid #000000; width: 100%; }
table.list .top-table { height: 50px; /*background: #000000; */color: #000000; }
table.list .alt1 { background: #000000; }
table.list .top-table th { border: 1px solid #000000; vertical-align: middle; padding: 3px; text-align: center; font-weight: 700; font-family: "Trebuchet MS", Tahoma, Arial, Verdana, Helvetica, sans-serif; }
table.list td, .list th { vertical-align: middle; }
table.list tr td { padding: 0 5px; }
    
table.list a { color: #000000; font-weight:100 }

table.list tr td { height: 30px; vertical-align: middle; padding: 3px; border: 1px solid #000000; text-align: center; }
table.list tr.deleted td, .list tr.deleted td input, .list tr.deleted td select { color: #000000; border-color: #000000; }
table.list tr.deleted td a { color: #000000; }

table.list tr.selected-bold { font-weight: bold; }
table.list tr.selected-bold a { font-weight: inherit; }

.title-field { font-weight: bold; height: 22px; width: 120px; float: left; padding: 5px 10px 5px 0; }
.value-field { height: 22px; padding: 5px 10px 5px 0; }

.form-td-title { width: 120px; text-align: right !important; }
.form-td-title-b { width: 120px; font-weight: bold; text-align: right !important; }
.form-td-title-i { width: 120px; font-style: italic; text-align: right !important; }
</style>
{/literal}
<body>
    <div class="content-main-container">
        <div class="cmc-page-name">{if !empty($page_name)}<h1>{$page_name}</h1>{/if}</div>
        {if !empty($content)}{$content}{/if}
    </div>
</body>
</html>