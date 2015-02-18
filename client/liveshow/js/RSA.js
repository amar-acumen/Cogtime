// RSA, a suite of routines for performing RSA public-key computations in
// JavaScript.
//
// Requires BigInt.js and Barrett.js.
//
// Copyright 1998-2005 David Shapiro.
//
// You may use, re-use, abuse, copy, and modify this code to your liking, but
// please keep this header.
//
// Thanks!
// 
// Dave Shapiro
// dave@ohdave.com 

function RSAKeyPair(encryptionExponent, modulus)
{
	this.e = biFromHex(encryptionExponent);
	//this.d = biFromHex(decryptionExponent);
	this.m = biFromHex(modulus);
	// We can do two bytes per digit, so
	// chunkSize = 2 * (number of digits in modulus - 1).
	// Since biHighIndex returns the high index, not the number of digits, 1 has
	// already been subtracted.
	this.chunkSize = 2 * biHighIndex(this.m);
	this.radix = 16;
	this.barrett = new BarrettMu(this.m);
}

function twoDigit(n)
{
	return (n < 10 ? "0" : "") + String(n);
}


function encryptedString(key, s)
	// Altered by Rob Saunders (rob@robsaunders.net). New routine pads the
	// string after it has been converted to an array. This fixes an
	// incompatibility with Flash MX's ActionScript.
{
	var length = s.length;
	var result = "";
	for (var i = 0; i < length; i += key.chunkSize) {
		var bi = new BigInt();
		var count = Math.min(length - i, key.chunkSize);
		for (var j = 0; j < count; j += 2) {
			var index = (count - j - 1);
			index = (index - index % 2) / 2;
			var sh = (s.charCodeAt(j + i)) << 8;
			var sl = 0;
			if (j + 1 < count) {
				sl = s.charCodeAt(j + i + 1);
			}
			bi.digits[index] = sh + sl;
		}
		var crypt = key.barrett.powMod(bi, key.e);
		var text = key.radix == 16 ? biToHex(crypt) : biToString(crypt, key.radix);
		if (result != "") {
			result += "l";
		}
		result += text;
	}
	return result;
}

function decryptedString(key, s)
{
	var blocks = s.split("l");
	var result = "";
	var i, j, block;
	for (i = 0; i < blocks.length; ++i) {
		var bi;
		if (key.radix == 16) {
			bi = biFromHex(blocks[i]);
		} else {
			bi = biFromString(blocks[i], key.radix);
		}
		block = key.barrett.powMod(bi, key.e);
		for (j = biHighIndex(block); j >= 0; j--) {
			result += String.fromCharCode(block.digits[j] >> 8);
			result += String.fromCharCode(block.digits[j] & 255);
		}
	}
	//return unescape(result);
	return result;
}
function fcEncode(hkey, strText) {
	var encText = "";
	var i = 0;
	var strlength = strText.length;
	var pwLength = hkey.length;
	for (var x = 0; x < strlength; x++) {
		if (x > 0) {
			encText += "l";
		}
		i = (i % pwLength) + 1;
		encText += (Number(strText.charCodeAt(x) ^ hkey.charCodeAt(i - 1)));
	}
	return encText;
}

function fcDecode(hkey, encText) {
	var strText = "";
	var crypt = encText.split("l");
	var i = 0;
	var cryptlength = crypt.length;
	var pwLength = hkey.length;
	for (x = 0; x < cryptlength; x++) {
		i = (i % pwLength) + 1;
		var hold = Number(crypt[x]) ^ hkey.charCodeAt(i - 1);
		strText += String.fromCharCode(hold);
	}
	return strText;
}

function genFCKey() {
	var keylen = 64;
	var keystr = "";
	for (var i = 0; i < keylen; i++) {
		var rndNum = Math.random();
		rndNum = parseInt(String(rndNum * 1000));
		rndNum = (rndNum % 94) + 33;
		//rndNum = (rndNum % 25) + 97; 
		var textChar = String.fromCharCode(rndNum);
		keystr += textChar;
	}
	return keystr;
}

function getEncryptedMsgWithRsaKeyPair(exponent, module, str)
{
	var key = new RSAKeyPair(exponent, module);
	return encryptedByPublicKeyString(key, str);
}

/*
function encryptedString(key, s)
	// Altered by Rob Saunders (rob@robsaunders.net). New routine pads the
	// string after it has been converted to an array. This fixes an
	// incompatibility with Flash MX's ActionScript.
{
	var a = new Array();
	var sl = s.length;
	var i = 0;
	while (i < sl) {
		a[i] = s.charCodeAt(i);
		i++;
	}

	while (a.length % key.chunkSize != 0) {
		a[i++] = 0;
	}

	var al = a.length;
	var result = "";
	var j, k, block;
	for (i = 0; i < al; i += key.chunkSize) {
		block = new BigInt();
		j = 0;
		for (k = i; k < i + key.chunkSize; ++j) {
			block.digits[j] = a[k++];
			block.digits[j] += a[k++] << 8;
		}
		var crypt = key.barrett.powMod(block, key.e);
		var text = key.radix == 16 ? biToHex(crypt) : biToString(crypt, key.radix);
		result += text + " ";
	}
	return result.substring(0, result.length - 1); // Remove last space.
}

function decryptedString(key, s)
{
	var blocks = s.split(" ");
	var result = "";
	var i, j, block;
	for (i = 0; i < blocks.length; ++i) {
		var bi;
		if (key.radix == 16) {
			bi = biFromHex(blocks[i]);
		}
		else {
			bi = biFromString(blocks[i], key.radix);
		}
		block = key.barrett.powMod(bi, key.d);
		for (j = 0; j <= biHighIndex(block); ++j) {
			result += String.fromCharCode(block.digits[j] & 255,
			                              block.digits[j] >> 8);
		}
	}
	// Remove trailing null, if any.
	if (result.charCodeAt(result.length - 1) == 0) {
		result = result.substring(0, result.length - 1);
	}
	return result;
}*/
