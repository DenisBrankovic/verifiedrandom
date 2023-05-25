	//==================================================================================================================================================
	// EVENT LISTENERS ON GENERATE BUTTON AND SAVE BUTTON 
	//==================================================================================================================================================
		
	var getGenerateBtn = document.getElementById("generateBtn");
	var getSaveBtn = document.getElementById("saveBtn");

	/* Gets all the div elements of the number generator (bottom part) where the generated results are displayed. These elements contain 
	the generated number, date and time, random number range (min, max), and the unique code. */
	
	var getAllResults = document.getElementsByClassName("allResults");	

	// Adds all the events to the generate button. 
	getGenerateBtn.addEventListener("click", getRandomNumber); 
	getGenerateBtn.addEventListener("click", displayDateTime);  
	getGenerateBtn.addEventListener("click", showHideResults);
	getGenerateBtn.addEventListener("click", saveUserInput);

	// Sets the visibility of the Save button to visible if user input validation returns true. 
	getGenerateBtn.addEventListener("click", ()=>{if(validation())getSaveBtn.style.visibility = "visible";}); 

	// Save button triggers unique code generation after user has created the desired number of random numbers and sends all the data to
	// the database via a hidden form. 
	getSaveBtn.addEventListener("click", getRandomCode);

	
	//=====================================================================================================================================================
	// USER INPUT VALIDATION 
	//=====================================================================================================================================================

	//Sets the text / number input border to red when validation returns false. 

	function markFieldRed(field){
		field.style.borderColor = "red"; 
	}

	// Unmarks the text / number input when it gets focus 

	function unmarkField(){
		this.style.borderColor = "initial"; 
		this.placeholder = ""; 
	}

	// Gets the text and number input fields

	let getName = document.getElementById("name");
	let getMinTxt = document.getElementById("minTxt");
	let getMaxTxt = document.getElementById("maxTxt");

	 
	getMinTxt.addEventListener("click", unmarkField);
	getMaxTxt.addEventListener("click", unmarkField);

	// Sets the default value to the number fields 

	getMinTxt.value = 1; 
	getMaxTxt.value = 100; 

	// Checks if the input fields contain values that can be parsed into integers; 
	// Prevents user from entering higher value into the min field; 
	// Returns false if any of the conditions are not met;
	// Returns true if all the conditions are satisfied. 
	// Hides the save button if the validation returns false;

	function validation(){

		// Gets the div elements which display the generated results (min - max range and the generated random number) 

		let getMinRes = document.getElementById("minRes");
		let getMaxRes = document.getElementById("maxRes");
		let getResult = document.getElementById("result");
				
		if(isNaN(parseInt(getMinTxt.value.trim()))){
			markFieldRed(getMinTxt); 
			getMinRes.innerHTML = "";
			getMaxRes.innerHTML = "";
			getResult.innerHTML = "";
			getSaveBtn.style.visibility = "hidden";
			getMinRes.placeholder = "Required filed"; 
			getName.style.fontSize = "100%"; 

			document.location.reload(); 
			
			return false; 
		}
		if(isNaN(parseInt(getMaxTxt.value.trim()))){
			markFieldRed(getMaxTxt);
			getMinRes.innerHTML = "";
			getMaxRes.innerHTML = ""; 
			getResult.innerHTML = "";
			getSaveBtn.style.visibility = "hidden";
			getMinRes.placeholder = "Required filed"; 
			getName.style.fontSize = "100%";

			document.location.reload(); 

			return false; 
		}
		if(parseInt(getMinTxt.value.trim()) >= parseInt(getMaxTxt.value.trim())){
			markFieldRed(getMinTxt);
			markFieldRed(getMaxTxt); 
			getSaveBtn.style.visibility = "hidden";
			alert("Min value must be lower than Max value."); 

			return false; 
		}
		return true; 
	}
	
	//=====================================================================================================================================================
	// SHOW OR HIDE THE DIV ELEMENTS WITH THE GENERATED RESULTS 
	//=====================================================================================================================================================

	// Every time a new random number is generated, the bottom part of the number generator gets hidden and displayed back again. It's handled by 
	// the showHideResults function, which alternates this value to true or false depending on the current state. 
	
	var resultsDisplayed = false; 
	
	// When a new unique code is created, the bottom segment of the number generator needs to stay displayed and show all the generated values.
	// The generated unique code is stored in the session storage at the "uniqueCode" index. 
	// This function checks if the session storage contains any value at the "uniqueCode" index and if it does, removes the "resultHidden" CSS class from
	// the div elements which contain the generated results and adds the "resultDisplayed" CSS class to them. 
	// Then it clears the session storage so that a new series of numbers and a new unique code can be generated and the result segment can be hidden. 

	(function(){
		
		if(sessionStorage.getItem("uniqueCode")){
			
			for(var all of getAllResults){                                
					all.classList.remove("resultHidden");
					all.classList.add("resultDisplayed");
				} 
		}
		sessionStorage.clear(); 
	})(); 

	// Shows and hides results with a small delay every time a new random number is generated. It also calls the showBlueRibbon function. 

	function showHideResults(){
		
			if(validation()){
				showBlueRibbon(); 

			if(resultsDisplayed == false){
				delayResultDisplay(); 
				resultsDisplayed = true;
			}else{
				for(var all of getAllResults){
				all.classList.remove("resultDisplayed");
				all.classList.add("resultHidden");
			}
				delayResultDisplay();
			}
		}
	}

	// This function gets called with a 900 milliseconds delay inside the above showHideResults function  
	 
	function displayResults(){
				
			if(validation()){
					for(var all of getAllResults){
					all.classList.remove("resultHidden");
					all.classList.add("resultDisplayed");
				}
					resultsDisplayed = true; 
			}
	}

	// Delays the execution of the displayResults function for 900 milliseconds 

	function delayResultDisplay(){
		setTimeout(displayResults, 900); 
	}

	//=====================================================================================================================================================
	// RIBBON DISPLAY FUNCTIONS 
	//=====================================================================================================================================================

	// The ribbon that spreads at the top of the bottom segment of the number generator every time a new number gets generated. These functions handle
	// the behavior of the ribbon. It's called "blue ribbon", but it's actually grey. Blue was just an initial variant, which was changed to grey, but
	// the function names and the id of the ribbon stayed unchanged. 

	function showBlueRibbon(){
		if(validation()){
			var getBlue = document.getElementById("blue");
			getBlue.style.transition = "all 0.8s ease-in-out"; 
			getBlue.style.width = "100%"; 
			delayedHideBlueRibbon(); 
		}
	}

	function delayedHideBlueRibbon(){
		setTimeout(hideBlueRibbon, 800); 
	}

	function hideBlueRibbon(){
		var getBlue = document.getElementById("blue");
		getBlue.style.transition = "all 0s"; 
		getBlue.style.width = "0%";
	}
	

	//====================================================================================================================================================
	// RANDOM NUMBER GENERATION, DATE AND TIME AND UNIQUE CODE 
	//====================================================================================================================================================
	
	// Counts the generated random numbers 
	var rndNumberCounter = 0; 


	// Generates a new random number within the user defined range and assignes the value to the "result" div. 
	// Also, increases the counter for every generated random number and displays it inside the "generate" button. 
	// Clears the code if the user just continued with a new sequence of numbers in the same session. 

	function getRandomNumber(){
		let getMinTxt = document.getElementById("minTxt");
		let getMaxTxt = document.getElementById("maxTxt");
		let getMinRes = document.getElementById("minRes");
		let getMaxRes = document.getElementById("maxRes"); 
		let getResult = document.getElementById("result");
		let getCode = document.getElementById("code");
		 
		if(validation())rndNumberCounter++; 
		getGenerateBtn.innerHTML = `Generate ${rndNumberCounter}`; 
		
		let min = parseInt(getMinTxt.value);
		let max = parseInt(getMaxTxt.value); 

		let result = Math.floor(Math.random() * (max - min)) + min;
			
		getMinRes.innerHTML = min;
		getMaxRes.innerHTML = max;
		getResult.innerHTML = result;
		getCode.innerHTML = ""; 
	} 
	

	function prependZero(value){
		return value < 10 ? "0" + value : value; 
	}
	
	// Gets the current UTC time and date, formats its value to "Y/m/d H:min:s", separates date from time and displays them in the results segment
	// of the number generator.

	// It also creates and returns a datetime value in the "Y-m-d H:i:s" format, which is sent through the hidden form to the PHP script that stores
	// the data into the database. 

	function displayDateTime(){
		var getDate = document.getElementById("date");
		var getTime = document.getElementById("time");

		const d = new Date();
		
		var year = d.getUTCFullYear(); 
		var month = prependZero(d.getUTCMonth() + 1);
		var day = prependZero(d.getUTCDate());

		var hours = prependZero(d.getUTCHours());
		var minutes = prependZero(d.getUTCMinutes());
		var seconds = prependZero(d.getUTCSeconds()); 

		var date = `${year}/${month}/${day}`;
		var time = `${hours}:${minutes}:${seconds} UTC`; 

		var dateTime = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`; 
		
		getDate.innerHTML = date;
		getTime.innerHTML = time;

		return dateTime;  		 
	}
		
	// Creates and reurns a random unique code 

	function getRandomCode() {
		var getCode = document.getElementById("code");
		let getCodeHidden = document.getElementById("codeHidden");

		if(validation()){
			var result           = '';
			var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
			var charactersLength = characters.length;
			for ( var i = 0; i < 6; i++ ) {
			   result += characters.charAt(Math.floor(Math.random() * charactersLength));
			}
			 
			getCodeHidden.value = result; 
			sessionStorage.setItem("uniqueCode", result);
		}
				
		return result; 
	 }

	 //====================================================================================================================================================
	 // GETS USER INPUT AND PASSES IT TO THE HIDDEN FORM LOCATED INSIDE THE NUMBER GENERATOR 
	 //====================================================================================================================================================

	 // Creates arrays, which accept all the generated values (name, min - max range, random number, and time). 
	 // Arrays are used because the user has to be able to create multiple random numbers in one session and assign them to a single unique code. 
	 // The values are passed to the hidden form inside the number generator and 
	 // sent to the PHP script within the index.php page. The script parses the arrays and stores the parsed data into the database. 

	 const names = []; 
	 const minimums = [];
	 const maximums = [];
	 const results = [];
	 const times = [];
	 	 
	 function saveUserInput(){
	 	let getName = document.getElementById("name");
	 	let getMinTxt = document.getElementById("minTxt");
	 	let getMaxTxt = document.getElementById("maxTxt");
	 	let getResult = document.getElementById("result");
	 	
	 	let getNameHidden = document.getElementById("nameHidden");
	 	let getMinHidden = document.getElementById("minHidden");
	 	let getMaxHidden = document.getElementById("maxHidden"); 
	 	let getResultHidden = document.getElementById("resultHidden");
	 	let getTimeHidden = document.getElementById("timeHidden");
	 		 	
	 	let name = getName.value;
	 	let minimum = getMinTxt.value;
	 	let maximum = getMaxTxt.value;
	 	let result = getResult.innerHTML;
	 	let time = displayDateTime(); 
	 	
	 	names.push(name);
	 	minimums.push(minimum);
	 	maximums.push(maximum);
	 	results.push(result);
	 	times.push(time);
	 	
	 	getNameHidden.value = names;
	 	getMinHidden.value = minimums;
	 	getMaxHidden.value = maximums;
	 	getResultHidden.value = results;
	 	getTimeHidden.value = times; 	 	 
	 }

	 	 
	 