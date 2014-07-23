/*
//////////////////////////////////////////////
	This functions allow user to convert values between Inches & mm

	Programming		: DJ (Dmitry)

	Start Date		: 22 Feb 2005, DJ
	Last Changed	: 22 Feb 2005, DJ
//////////////////////////////////////////////

*/

// Преобразует вводимое значение из дюймов в мм
	function InchToMM(input)
	{
		var rate = 25.4, res = 0
		var inch_value = new String(input);
		
/*	Start AddOn: 2008.07.16, DJ:	Переводит значение 68.8 в 68 8/10, 68.85 в 68 85/100 и тд	*/
		var indx = inch_value.indexOf(".");
	
		if (indx > -1)
		{
			b 			= inch_value.substring(indx + 1, inch_value.length);
			devider 	= Math.pow(10, b.length);

			if (indx > 0)
			{
				a 			= inch_value.substring(0, indx);
				inch_value 	= a + " " + b + "/" + devider;
			}
			else
			{
				inch_value	= b + "/" + devider;
			}
		}

/*	End AddOn	*/
		
		var tstr = new String("");
		var rstr = new String("");
		var full, top, bottom
		
		r = RegExp("[^\\d \/]+", "g");
		inch_value = inch_value.replace("\\", "/");
		inch_value = Trim(inch_value.replace(r, ""));

		if (inch_value.indexOf("/", 0) > -1)
		{
			// 1. Подготавливаем строку к разбору
			tstr = inch_value.substring(0, inch_value.indexOf("/", 0))
			rstr = RTrim(tstr);
			tstr = inch_value.substring(inch_value.indexOf("/", 0)+1, inch_value.length)
			inch_value = rstr + "/" + tstr.replace(" ","");
			
			// 2. Выделяем целую часть
			if (inch_value.indexOf("/", 0) < inch_value.indexOf(" ", 0) || inch_value.indexOf(" ", 0) == -1)
			{
				full = 0;
			}
			else
			{
				full = inch_value.substring(0, inch_value.indexOf(" ", 0));
				inch_value = inch_value.substring(inch_value.indexOf(" ", 0), inch_value.length);
			}
			
			// 3. Выделяем числитель
			inch_value = inch_value.replace(" ", "");
			top = inch_value.substring(0, inch_value.indexOf("/", 0));
			
			// 4. Выделяем знаменатель
			bottom = inch_value.substring(inch_value.indexOf("/", 0)+1, inch_value.length).replace(" ","").replace("\"", "").replace("/", "").replace("''", "").replace("'","");
		}
		else
		{
			full = inch_value.replace(" ","").replace("\"", "").replace("/", "").replace("''", "").replace("'","");
			top = 0;
			bottom = 1;
		}
		
		// 5. Выводим результат
		//return RoundTo(parseFloat(full) * rate + (parseFloat(top) * rate / parseFloat(bottom)), 0);	убрал 19.12.2005 т.к. показалось, что неправильно считает
		return parseFloat(full) * rate + (parseFloat(top) * rate / parseFloat(bottom));
	}

// Преобразует вводимое значение из мм в дюймы
	function MMToInch(input)
	{
		r = RegExp("[^\\d\\.]+", "g");
		var str = new String(input);
		var mm_value = parseFloat(str.replace(r, ""));
		var res = new String("");
		var rate = 25.4;
		var prec = 16;	// точность вычисления
		var i = 2;
		var fullpart, ostatok, min, t, top, bottom, tt, tb;
		
		ostatok = RoundTo((mm_value / rate) - RowndTo(mm_value / rate, 0), 4);
		fullpart = RowndTo(mm_value / rate, 0);
		
		min = 1;
		while (i <= prec)
		{
			t = 1 / parseFloat(i);
			t = ostatok / t;
			top = RoundTo(t, 0);
			bottom = Math.abs(RoundTo(t - RoundTo(t, 0), 4));
			if (bottom < min)
			{
				min = bottom;
				tt = top;
				tb = i;
			}
		i++
		}
		top = tt;
		bottom = tb;
		if (top == bottom) fullpart = fullpart + 1;
		if (fullpart > 0) res = res + fullpart;
		if (res.length > 0) res = res + " ";
		if (top > 0 && top != bottom) res = res + top + "/" + bottom;
		return res + " \"";
	};
    
// Cut float-type value to setted number of digits after comma
function RowndTo(val, count)
{
    s = new String(val);
    var c = s.indexOf(".", 0)+1;
    if (c > 0)
    {
        ss = s.substr(c,s.length);
        if (parseInt(count) < parseInt(ss.length))
            return parseFloat(s.substr(0, c+count));
        else 
            return parseFloat(val);
    }
    else
        return parseFloat(val);
}

// Round float-type value to setted number of digits after comma
function RoundTo(val, count)
{
    return Math.round(val * Math.pow(10,count))/Math.pow(10,count);
}
    
function RTrim(str)
{
    re = new RegExp("\\s+$");
    return str.replace(re, "");
}

function LTrim(str)
{
    re = new RegExp("^\\s+");
    return str.replace(re, "");
}

function Trim(str)
{
    return RTrim(LTrim(str));
}
    
    