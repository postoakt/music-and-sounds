function iif(b, t, f){
	if (b){
		return t;
	}
	else{
		return f;
	}
}

function WordCount(str) { 
  return str.split(" ").length;
}

function RandomInteger(min, max){
	if (isNaN(min)){
		min = -Number.MAX_SAFE_INTEGER;
	}
	if (isNaN(max)){
		max = Number.MAX_SAFE_INTEGER;
	}
	return Math.floor(Math.random() * (max - min + 1)) + min;
}