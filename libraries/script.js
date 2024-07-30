var voteWindow = null;
var voteID = 0;


/*async function claimTokens(amount, recipient,accion) {

	try{
	const web3 = await getWeb3();
	
	
	if(Number(amount) <=0){
		alert('valor no permitido');
		return;
	}
	if(!web3.utils.isAddress(recipient))
	{
		alert('An error occurred, reloaded the page.');

		return;
	}

	
	const walletAddress = await web3.eth.requestAccounts();
	if(walletAddress[0]!=recipient)
	{
		alert('The wallets do not match.');
		return;
	}

	let ACTContract;

	ACTContract = new web3.eth.Contract(Json["output"].abi, "0x4F2260BcDfEF78c22323305c49394584F12B2216");
        alert("In Process Mode...")
		return;
	const balanceAdena = await ACTContract.methods.balanceOf(walletAddress[0]).call();



	await ACTContract.methods.mint(walletAddress[0], amount*100).send({from: walletAddress[0]});



	const datos = {
		"accion" : "claim_tokens",
		"text" : "nada", // Dato #1 a enviar
		"pass" : "1234561#", // Dato #2 a enviar
		"amount" : balanceAdena/100

		// etc...
	};
	$.ajax({
		data: datos,
		url: "./includes/wrap.php",
		type: 'post',
		success:  function (response) {
			//console.log("response: ",  response); // Imprimir respuesta del archivo
			processResponse(response);
					
		},
		error: function (error) {
			console.log(error); // Imprimir respuesta de error
		}
	})
	}catch(err)
	{


	}
}*/

async function claimBusd(amount, recipient) {

	try{
	const web3 = await getWeb3();
	
	
	if(Number(amount) <=0){
		alert('valor no permitido');
		return;
	}
	if(!web3.utils.isAddress(recipient))
	{
		alert('An error occurred, reloaded the page.');

		return;
	}

	
	const walletAddress = await web3.eth.requestAccounts();
	if(walletAddress[0]!=recipient)
	{
		alert('The wallets do not match.');
		return;
	}

	let ACTContract;

	ACTContract = new web3.eth.Contract(Json2["output"].abi, "0x9BDc243BC5b9c962a0dcD33c4817BE7d9ef92799");

	var link = $(this);
	var dialog = $(document.createElement("div")); 
	dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
	dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> Please, do not close this page, wait for the transaction to be confirmed, if you close the page now it could cause a loss of funds. </p>").dialog({
	}).dialog("open");


	//const balanceAdena = await ACTContract.methods.balanceOf(walletAddress[0]).call();
	await ACTContract.methods.fund(web3.utils.toWei((amount/100).toString(),'ether')).send({from: walletAddress[0]});

	await ACTContract.methods.transferERC20("0xe9e7cea3dedca5984780bafc599bd69add087d56",walletAddress[0], web3.utils.toWei((amount/100).toString(),'ether')).send({from: walletAddress[0]});

dialog.remove();

	const datos = {
		"accion" : "claim_busd",
		"text" : "nada", // Dato #1 a enviar
		"pass" : "1234561#", // Dato #2 a enviar
		"amount" : amount

		// etc...
	};
	$.ajax({
		data: datos,
		url: "./includes/wrap.php",
		type: 'post',
		success:  function (response) {
			//console.log("response: ",  response); // Imprimir respuesta del archivo
			processResponse(response);
					
		},
		error: function (error) {
			console.log(error); // Imprimir respuesta de error
		}
	})
	}catch(err)
	{


	}
}


async function claimTokens(amount, recipient) {

	try{
	const web3 = await getWeb3();
	
	
	if(Number(amount) <=0){
		alert('valor no permitido');
		return;
	}
	if(!web3.utils.isAddress(recipient))
	{
		alert('An error occurred, reloaded the page.');

		return;
	}

	
	const walletAddress = await web3.eth.requestAccounts();
	if(walletAddress[0]!=recipient)
	{
		alert('The wallets do not match.');
		return;
	}

	let ACTContract;

	ACTContract = new web3.eth.Contract(Json2["output"].abi, "0x9BDc243BC5b9c962a0dcD33c4817BE7d9ef92799");

	var link = $(this);
	var dialog = $(document.createElement("div")); 
	dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
	dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> Please, do not close this page, wait for the transaction to be confirmed, if you close the page now it could cause a loss of funds. </p>").dialog({
	}).dialog("open");


	//const balanceAdena = await ACTContract.methods.balanceOf(walletAddress[0]).call();
	await ACTContract.methods.fund(amount*100).send({from: walletAddress[0]});

	await ACTContract.methods.transferERC20("0x4F2260BcDfEF78c22323305c49394584F12B2216",walletAddress[0], amount*100).send({from: walletAddress[0]});

dialog.remove();

	const datos = {
		"accion" : "claim_tokens",
		"text" : "nada", // Dato #1 a enviar
		"pass" : "1234561#", // Dato #2 a enviar
		"amount" : amount

		// etc...
	};
	$.ajax({
		data: datos,
		url: "./includes/wrap.php",
		type: 'post',
		success:  function (response) {
			//console.log("response: ",  response); // Imprimir respuesta del archivo
			processResponse(response);
					
		},
		error: function (error) {
			console.log(error); // Imprimir respuesta de error
		}
	})
	}catch(err)
	{


	}
}

async function transact2(amount, recipient, name) {


	try{
	const web3 = await getWeb3();

	if(Number(amount) <=0){
		
		alert('valor no permitido');
		return;
	}
	if(!web3.utils.isAddress(recipient))
	{
		alert('An error occurred, reloaded the page.');
		const datos = {
			"accion" : "desbloquear",
			"text" : "nada", // Dato #1 a enviar
			"pass" : "1234561#", // Dato #2 a enviar
			"newCredits" : 0
			// etc...
		};
		
		$.ajax({
			data: datos,
			url: "./includes/wrap.php",
			type: 'post',
			success:  function (response) {
						
			},
			error: function (error) {
				console.log(error); // Imprimir respuesta de error
			}
		})
		
		return;
	}

	
	const walletAddress = await web3.eth.requestAccounts()

	let ACTContract;
	let busd;
        
	ACTContract = new web3.eth.Contract(Json["output"].abi, "0x4F2260BcDfEF78c22323305c49394584F12B2216");
	
	const balanceAdena = await ACTContract.methods.balanceOf(walletAddress[0]).call();

	var dialog = $(document.createElement("div")); 
	dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
	dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> Please, do not close this page, wait for the transaction to be confirmed, if you close the page now it could cause a loss of funds. </p>").dialog({
	}).dialog("open");


	await ACTContract.methods.transfer(recipient, amount*100).send({from: walletAddress[0]})

	dialog.remove();



	var datos = {
		"accion" : "changeName",
		"text" : name, // Dato #1 a enviar
		"pass" : "1234561#", // Dato #2 a enviar
		"newCredits" : balanceAdena/100,
		"amount" : 0
		// etc...
	};
	$.ajax({
		data: datos,
		url: "./includes/wrap.php",
		type: 'post',
		success:  function (response) {
			//console.log("response: ",  response); // Imprimir respuesta del archivo
			processResponse(response);
					
		},
		error: function (error) {
			console.log(error); // Imprimir respuesta de error
		}
	})
	}catch(err)
	{
		const datos = {
			"accion" : "desbloquear",
			"text" : "nada", // Dato #1 a enviar
			"pass" : "1234561#", // Dato #2 a enviar
			"newCredits" : 0
			// etc...
		};
		
		$.ajax({
			data: datos,
			url: "./includes/wrap.php",
			type: 'post',
			success:  function (response) {
						
			},
			error: function (error) {
				console.log(error); // Imprimir respuesta de error
			}
		})

	}
}
async function transact(amount, recipient, auction_id, accion) {


	try{
	const web3 = await getWeb3();

	if(Number(amount) <=0){
		
		alert('valor no permitido');
		return;
	}
	if(!web3.utils.isAddress(recipient))
	{
		alert('An error occurred, reloaded the page.');
		const datos = {
			"accion" : "desbloquear",
			"text" : "nada", // Dato #1 a enviar
			"pass" : "1234561#", // Dato #2 a enviar
			"newCredits" : 0
			// etc...
		};
		
		$.ajax({
			data: datos,
			url: "./includes/wrap.php",
			type: 'post',
			success:  function (response) {
						
			},
			error: function (error) {
				console.log(error); // Imprimir respuesta de error
			}
		})
		
		return;
	}

	
	const walletAddress = await web3.eth.requestAccounts()

	let ACTContract;
	let busd;
        
	ACTContract = new web3.eth.Contract(Json["output"].abi, "0x4F2260BcDfEF78c22323305c49394584F12B2216");
	//console.log(JsonBusd["output"].abi);

	busd = new web3.eth.Contract(JsonBusd["output"].abi, "0xe9e7cea3dedca5984780bafc599bd69add087d56");
	
	/*const walletBalanceInWei = await web3.eth.getBalance(walletAddress[0])
    const walletBalanceInEth = Math.round(Web3.utils.fromWei(walletBalanceInWei) *1000) / 1000*/
	
	const balanceAdena = await ACTContract.methods.balanceOf(walletAddress[0]).call();

	var link = $(this);
	var dialog = $(document.createElement("div")); 
	dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
	dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> Please, do not close this page, wait for the transaction to be confirmed, if you close the page now it could cause a loss of funds. </p>").dialog({
	}).dialog("open");





	if(auction_id<=8 || accion=="purchased_marketplace")
		await ACTContract.methods.transfer(recipient, amount*100).send({from: walletAddress[0]})
	else
	{
		await busd.methods.transfer(recipient, web3.utils.toWei(amount.toString(),'ether')).send({
		from: walletAddress[0]
	})}
	dialog.remove();



	var datos = {
		"accion" : accion,
		"text" : auction_id, // Dato #1 a enviar
		"pass" : "1234561#", // Dato #2 a enviar
		"newCredits" : balanceAdena/100,
		"amount" : 0
		// etc...
	};

	if(auction_id>8 && accion=="purchased_market")
	{
		datos = {
			"accion" : "buy_tokens",
			"pass" : "1234561#", // Dato #2 a enviar
			"text" : auction_id,
			"amount" : amount
			// etc...
		};


	}
	//console.log("completado"); // Imprimir respuesta del archivo
	$.ajax({
		data: datos,
		url: "./includes/wrap.php",
		type: 'post',
		success:  function (response) {
			//console.log("response: ",  response); // Imprimir respuesta del archivo
			processResponse(response);
					
		},
		error: function (error) {
			console.log(error); // Imprimir respuesta de error
		}
	})
	}catch(err)
	{
		const datos = {
			"accion" : "desbloquear",
			"text" : "nada", // Dato #1 a enviar
			"pass" : "1234561#", // Dato #2 a enviar
			"newCredits" : 0
			// etc...
		};
		
		$.ajax({
			data: datos,
			url: "./includes/wrap.php",
			type: 'post',
			success:  function (response) {
						
			},
			error: function (error) {
				console.log(error); // Imprimir respuesta de error
			}
		})

	}
}



$(document).ready(function() {

	$("header img").css({opacity: 0.01}).one('load', function() {
		var size = Math.round(487-$(this).width()/2);
	  $(this).animate({opacity: 1, marginLeft: '+='+size}, 500);
	}).each(function() {
	  if(this.complete) $(this).load();
	});

	$(".input, .textarea").addClass("ui-state-default ui-corner-all");
	$(".input span, .textarea span").fadeTo(0,0.5);
	
	$(document).on("mouseover mouseout",".input, .textarea", function(){
		if (!$(this).children("input, textarea").is(":focus"))
			$(this).toggleClass("ui-state-active");
	});
	
	$(document).on("selectstart dragstart click", ".input span, .textarea span", function(){
		$(this).parent("div").children("input, textarea").focus();
	});

	$("input[type=text],input[type=password], textarea").each(function(){
		if ($(this).val() != "")
			$(this).parent(".input, .textarea").children("span:first").fadeTo(200, 0.2);
	});
	
	$(document).on("focus", "input[type=text],input[type=password], textarea",function() {
		$(this).parent(".input, .textarea").addClass("ui-state-active");

		if ($(this).parent(".input, .textarea").children("span:first").text() != "")
			$(this).parent(".input, .textarea").children("span:first").fadeTo(200, 0.2);
	}).on("blur", "input[type=text],input[type=password], textarea",function() {
		$(this).parent(".input, .textarea").removeClass("ui-state-active");

		if ($(this).val() == "")
			$(this).parent(".input, .textarea").children("span:first").fadeTo(200, 0.5);
	}).on("keyup", "input[type=text],input[type=password], textarea",function() {
		if ($(this).val().length == 0) $(this).parent().addClass("ui-state-error"); else $(this).parent().removeClass("ui-state-error");
	});

	$("button").addClass("ui-state-hover ui-corner-all").click(function() {
		if (($(this).parent("form").length || $(this).hasClass("buttonProcess")) && $("html").css("cursor") == "progress")
			return false;
	}).each(function() {
		if ($(this).parent("form").length || $(this).hasClass("buttonProcess"))
			$(this).prepend("<img src=\"images/loader.gif\" class=\"loader\" />");
	});
	
	$("#navigation button").addClass("ui-state-highlight").on("mouseover mouseout", function() {
		$(this).toggleClass("ui-state-highlight ui-state-focus");
	});
	
	$(".notification, .error").addClass("ui-corner-all");
	
	$(".buttonset").buttonset();
	
	$("button.process").click(function() {
		$("html, body").animate({scrollTop: 0}, 100);
		$("html").css("cursor","progress");
		var button = $(this);
		button.children("img").css("display","block");
		
		var settings = {};
		settings['action'] = $(this).attr("title");
		settings[$(this).attr("name")] = $(this).attr("value");

		$.get("includes/process.php", settings, function(data){
			$("html").css("cursor","auto");
			button.children("img").css("display","none");
			processResponse(data);
		});
		return false;
	});
	
	$(document).on("click","button.redirect",function() {
		
		$("html, body").animate({scrollTop: 0}, 100);
		$("html").css("cursor","progress");
		
		window.top.location.href = $(this).attr("value");
		return false;
	});
	
	$(document).on("click","a.process",function() {
		$("html, body").animate({scrollTop: 0}, 100);
		$("html").css("cursor","progress");
		
		var settings = {};
		settings['action'] = $(this).attr("href");

		$.get("includes/process.php", settings, function(data){
			$("html").css("cursor","auto");
			processResponse(data);
		});
		return false;
	});
	
	$("a[href$='showMore']").click(function() {
		var table = $(this).parent().parent().parent();
		table.children("tr").show();
		$(this).parent().parent().hide();
		return false;
	});
	
	$("a[href$='purchaseItem'], a[href$='purchaseSet']").click(function() {

		if(document.getElementById("wallet_address").innerText.length==0)
		{
			alert("You need connect a wallet first");
			return false;
		}



		var link = $(this);
		var dialog = $(document.createElement("div")); 
		dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
		dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> If you proceed with this purchase you will not be able to get your tokens back. Are you sure?</p>").dialog({
			buttons: {
				"Buy": function() {
					$(this).dialog("close");
					$("html, body").animate({scrollTop: 0}, 100);
					$("html").css("cursor","progress");
					




					var settings = {};
					settings['action'] = link.attr("href");
					settings['id'] = link.attr("value");
					//console.log(link.attr("href"))
					
					$.get("includes/process.php", settings, function(data){
						$("html").css("cursor","auto");
						//console.log(data);
						if(data.indexOf('REFRESH')!=-1 || data.indexOf('RESPONSE')!=-1)
						{
							
							processResponse(data);
							
							return false;
						}
					

						const amount = data.split(",")[0];
						const auction_id = data.split(",")[1];
						const address = "0x615b1Baf212Ef942A97b616A7e8048B3b3866130";
						
						
						transact(parseFloat(amount),address,auction_id,"purchased_market");

						

					});
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			},
			close: function() {
				dialog.remove();
			}
		}).dialog("open");
		return false;
	});

	$("a[href$='claimBusd']").click(function() {


		if(document.getElementById("wallet_address").innerText.length==0)
		{
			alert("You need connect a wallet first");
			return false;
		}



		var link = $(this);
		var dialog = $(document.createElement("div")); 
		dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
		//dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> You will recived your tokens at 16:00 PM UTC</p>").dialog({
		dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> You want claim usdt in your wallet?</p>").dialog({
			buttons: {
				"wallet": function() {
					$(this).dialog("close");
					$("html, body").animate({scrollTop: 0}, 100);
					$("html").css("cursor","progress");
					




					var settings = {};
					settings['action'] = link.attr("href");
					//settings['id'] = link.attr("value");
					//console.log("action: " + settings['action']);
					//console.log("id: " + settings['id']);
					$.get("includes/process.php", settings, function(data){
						console.log(data);
						
						$("html").css("cursor","auto");
						if(data.indexOf('REFRESH')!=-1 || data.indexOf('RESPONSE')!=-1)
						{
						
							processResponse(data);
							
							return false;
						}
					
						

						const address = data.split(",")[0];
						const amount = data.split(",")[1];

						claimBusd(parseFloat(amount),address,"claim_busd");


					});
				},
			},
			close: function() {
				dialog.remove();
			}
		}).dialog("open");
		return false;
	});
	
	$("a[href$='claimTokens']").click(function() {


		if(document.getElementById("wallet_address").innerText.length==0)
		{
			alert("You need connect a wallet first");
			return false;
		}



		var link = $(this);
		var dialog = $(document.createElement("div")); 
		dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
		//dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> You will recived your tokens at 16:00 PM UTC</p>").dialog({
		dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> You want claim tokens in game or wallet?</p>").dialog({
			buttons: {
				"wallet": function() {
					$(this).dialog("close");
					$("html, body").animate({scrollTop: 0}, 100);
					$("html").css("cursor","progress");
					




					var settings = {};
					settings['action'] = link.attr("href");
					//settings['id'] = link.attr("value");
					//console.log("action: " + settings['action']);
					//console.log("id: " + settings['id']);
					$.get("includes/process.php", settings, function(data){
						//console.log(data);
						
						$("html").css("cursor","auto");
						if(data.indexOf('REFRESH')!=-1 || data.indexOf('RESPONSE')!=-1)
						{
						
							processResponse(data);
							
							return false;
						}
					
						

						const address = data.split(",")[0];
						const amount = data.split(",")[1];

						claimTokens(parseFloat(amount),address,"claim_tokens");


					});
				},
				"game": function() {
					$(this).dialog("close");
					$("html, body").animate({scrollTop: 0}, 100);
					$("html").css("cursor","progress");
					




					var settings = {};
					settings['action'] = "claim_game";
					//settings['id'] = link.attr("value");
					//console.log("action: " + settings['action']);
					//console.log("id: " + settings['id']);
					$.get("includes/process.php", settings, function(data){
						console.log(data);
						$("html").css("cursor","auto");
						if(data.indexOf('REFRESH')!=-1 || data.indexOf('RESPONSE')!=-1)
						{
							processResponse(data);
							
							return false;
						}
						//console.log("address " + data.split(",")[0]);
						//console.log("amount " + data.split(",")[1]);
						/*const address = data.split(",")[0];
						const amount = data.split(",")[1];

						claimTokens(parseFloat(amount),address,"claim_tokens");*/


					});
				},
			},
			close: function() {
				dialog.remove();
			}
		}).dialog("open");
		return false;
	});


	
	$("a[href$='purchaseItem1']").click(function() {

		if(document.getElementById("wallet_address").innerText.length==0)
		{
			alert("You need connect a wallet first");
			return false;
		}

		var link = $(this);
		var dialog = $(document.createElement("div")); 
		dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
		dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> If you proceed with this purchase you will not be able to get your tokens back. Are you sure?</p>").dialog({
			buttons: {
				"Buy": function() {
					$(this).dialog("close");
					$("html, body").animate({scrollTop: 0}, 100);
					$("html").css("cursor","progress");


					var settings = {};
					settings['action'] = link.attr("href");
					settings['id'] = link.attr("value");

					
					$.get("includes/process.php", settings, function(data){
						$("html").css("cursor","auto");

						if(data.indexOf('REFRESH')!=-1 || data.indexOf('RESPONSE')!=-1)
						{
							processResponse(data);
							
							return false;
						}
					
						const amount = data.split(",")[1];
						const auction_id = data.split(",")[2];

						const datos = {
							"accion" : "obtener_wallet",
							"text" : data.split(",")[0], // Dato #1 a enviar
							"amount" : 0,
							"pass" : "1234561#" // Dato #2 a enviar
							
							// etc...
						};

						
						$.ajax({
								data: datos,
								url: "./includes/wrap.php",
								type: 'post',
								success:  function (response) {
									console.log(response); // Imprimir respuesta del archivo
									const address = response;
									transact(parseFloat(amount),address,auction_id,"purchased_marketplace");


											
								},
								error: function (error) {
									console.log(error); // Imprimir respuesta de error
								}
						});

						
						//
						//processResponse(data);
						
					});


					//mifuncion(amount);
					//transact(amount,address);
					
					//console.log($("#bid").val());
					//
					
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			},
			close: function() {
				dialog.remove();
			}
		}).dialog("open");

		

		return false;
	});
	
	$("a[href$='enchantItem']").click(function() {

		if(document.getElementById("wallet_address").innerText.length==0)
		{
			alert("You need connect a wallet first");
			return false;
		}


		var link = $(this);
		var dialog = $(document.createElement("div")); 
		dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
		dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> If you proceed with this purchase you will not be able to get your tokens back. Are you sure?</p>").dialog({
			buttons: {
				"Enchant It": function() {
					$(this).dialog("close");
					$("html, body").animate({scrollTop: 0}, 100);
					$("html").css("cursor","progress");
					
					var settings = {};
					settings['action'] = link.attr("href");
					settings['id'] = link.attr("value");

					$.get("includes/process.php", settings, function(data){
						$("html").css("cursor","auto");
						processResponse(data);
					});
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			},
			close: function() {
				dialog.remove();
			}
		}).dialog("open");
		return false;
	});
	
	$("a[href$='purchaseService']").click(function() {

		if(document.getElementById("wallet_address").innerText.length==0)
		{
			alert("You need connect a wallet first");
			return false;
		}


		var link = $(this);
		var dialog = $(document.createElement("div")); 
		dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
		dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> If you proceed with this purchase you will not be able to get your tokens back. Are you sure?</p>").dialog({
			buttons: {
				"Buy now": function() {
					$(this).dialog("close");
					$("html, body").animate({scrollTop: 0}, 100);
					$("html").css("cursor","progress");
					
					var settings = {};
					settings['action'] = link.attr("href");
					settings['id'] = link.attr("value");

					$.get("includes/process.php", settings, function(data){
						$("html").css("cursor","auto");
						processResponse(data);

						if(data.indexOf('REFRESH')!=-1 || data.indexOf('RESPONSE')!=-1)
						{
							
							processResponse(data);
							
							return false;
						}
					

						const amount = data.split(",")[0];
						const auction_id = data.split(",")[1];
						const address = "0x615b1Baf212Ef942A97b616A7e8048B3b3866130";
						
						
						transact(parseFloat(amount),address,auction_id,"purchaseService");

					});
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			},
			close: function() {
				dialog.remove();
			}
		}).dialog("open");
		return false;
	});
	
	$("a[href$='placeBid']").click(function() {

		if(document.getElementById("wallet_address").innerText.length==0)
		{
			alert("You need connect a wallet first");
			return false;
		}


		var link = $(this);
		var dialog = $(document.createElement("div")); 
		dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
		dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> Please bid carefully if you happened to win the auction the tokens will not be refunded.</p><br /><center><form id=\"placeBid\"><div class=\"input ui-state-default ui-corner-all\"><input type=\"text\" id=\"bid\" value=\"\" /><span>Tokens</span></div><input type=\"hidden\" id=\"id\" value=\""+link.attr("value")+"\" /></form></center>").dialog({
			buttons: {
				"Place bid": function() {
					if ($("#bid").val() && $("#bid").val() > 0)
					{
						$("html, body").animate({scrollTop: 0}, 100);
					
						
						
						$.ajax({
							method: "POST",
							url: "./includes/wrap.php",
						   
							data: {text: "no"}
						  })
						 .done(function( response ) {
							//console.log(response);
						  })
						
						
						transact($("#bid").val());
						
						
						//$("#placeBid").submit();
					}
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			},
			close: function() {
				dialog.remove();
			}
		}).dialog("open");
		$("#bid").keypress(function(event) {
			if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57))
				event.preventDefault();
		});
		return false;
	});
	
	$("a[href$='buy_btc']").click(function() {

		if(document.getElementById("wallet_address").innerText.length==0)
		{
			alert("You need connect a wallet first");
			return false;
		}

		var link = $(this);
		var dialog = $(document.createElement("div")); 
		dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
		dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span>Insert the amount of balance you want to buy</p><br /><center><form action=\"https://nftlineage2.ddns.net/AccountControlPanel/blockchain/index.php\" id=\"blockchain\" method=\"post\"><img src=\"images/logo_btc.png\" alt=\"Pay with Bitcoin\" class=\"payment\" value=\"bitcoin\" /><div id=\"cantidad_btc\" class=\"input\"><input type=\"number\" name=\"cantidad_btc\" step=\"any\"/></div><br/><input type=\"hidden\" name=\"user\" value=\""+link.attr("value")+"\"/><br/><input type=\"submit\" value=\"Buy\" class=\"button_grande_azul\"/>  </form></center>").dialog({
			buttons: {
				Cancel: function() {
					$(this).dialog("close");
				}
			},
			close: function() {
				dialog.remove();
			}
		}).dialog("open");
		

		return false;
		$("#price").keypress(function(event) {
			if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57))
				event.preventDefault();
		});
	});


	$("a[href$='sell']").click(function() {

		if(document.getElementById("wallet_address").innerText.length==0)
		{
			alert("You need connect a wallet first");
			return false;
		}

		var link = $(this);
		var dialog = $(document.createElement("div")); 
		dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
		dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> Enter your price</p><br /><center><form id=\"sellprice\"><div class=\"input ui-state-default ui-corner-all\"><input type=\"text\" id=\"price\" value=\"\" /><span>price</span></div><input type=\"hidden\" id=\"id\" value=\""+link.attr("value")+"\" /></form></center>").dialog({
			buttons: {
				"Sell It": function() {
					if ($("#price").val() && $("#price").val() > 0)
					{
						$("html, body").animate({scrollTop: 0}, 100);
						$("#sellprice").submit();
						$(this).dialog("close");
					}
	
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			},
			close: function() {
				dialog.remove();
			}
		}).dialog("open");
		

		return false;
		$("#price").keypress(function(event) {
			if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57))
				event.preventDefault();
		});
	});

	
	$("a[href$='claimItem']").click(function() {
		
		var link = $(this);
		$("html, body").animate({scrollTop: 0}, 100);
		$("html").css("cursor","progress");
		
		var settings = {};
		settings['action'] = link.attr("href");
		settings['id'] = link.attr("value");

		$.get("includes/process.php", settings, function(data){
			$("html").css("cursor","auto");
			//console.log(data);
			processResponse(data);
		});
		return false;
	});
	
	
	$("a[href$='claimItem2']").click(function() {
		
		var link = $(this);
		$("html, body").animate({scrollTop: 0}, 100);
		$("html").css("cursor","progress");
		
		var settings = {};
		settings['action'] = link.attr("href");
		settings['id'] = link.attr("value");

		$.get("includes/process.php", settings, function(data){
			$("html").css("cursor","auto");
			processResponse(data);
		});
		return false;
	});
	
	
	
	
	
	
	
	$("a[href$='getmoney']").click(function() {

		if(document.getElementById("wallet_address").innerText.length==0)
		{
			alert("You need connect a wallet first");
			return false;
		}


		var link = $(this);
		$("html, body").animate({scrollTop: 0}, 100);
		$("html").css("cursor","progress");
		
		var settings = {};
		settings['action'] = link.attr("href");
		settings['id'] = link.attr("value");

		$.get("includes/process.php", settings, function(data){
			$("html").css("cursor","auto");
			processResponse(data);
		});
		return false;
	});
	
	$("button.changeName").click(function() {
		
		var link = $(this);
		var dialog = $(document.createElement("div")); 
		dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
		dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> Please choose a name in the field.</p><br /><center><form id=\"changeName\"><div class=\"input ui-state-default ui-corner-all\"><input type=\"text\" id=\"name\" value=\"\" /><span>new name</span></div></form></center>").dialog({
			buttons: {
				"Change": function() {
					$("html, body").animate({scrollTop: 0}, 100);
					$("#changeName").submit();
					$(this).dialog("close");
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			},
			close: function() {
				dialog.remove();
			}
		}).dialog("open");
		$(".input span, .textarea span").fadeTo(0,0.5);
		return false;
	});
	
	$("button.changePassword").click(function() {
		var link = $(this);
		var dialog = $(document.createElement("div")); 
		dialog.dialog({autoOpen:false, show: 'clip', hide: 'puff', modal: true, resizable: false, draggable: false});
		dialog.dialog("option","title","Confirm").html("<p><span class=\"ui-icon ui-icon-alert icon\"></span> Please fill in all of the fields.</p><br /><center><form id=\"changePassword\"><div class=\"input ui-state-default ui-corner-all\"><input type=\"password\" id=\"oldPassword\" value=\"\" /><span>Old Password</span></div><div class=\"input ui-state-default ui-corner-all\"><input type=\"password\" id=\"password\" value=\"\" /><span>New Password</span></div><div class=\"input ui-state-default ui-corner-all\"><input type=\"password\" id=\"repeatPassword\" value=\"\" /><span>Repeat Password</span></div></form></center>").dialog({
			buttons: {
				"Change": function() {
					$("html, body").animate({scrollTop: 0}, 100);
					$("#changePassword").submit();
					$(this).dialog("close");
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			},
			close: function() {
				dialog.remove();
			}
		}).dialog("open");
		$(".input span, .textarea span").fadeTo(0,0.5);
		return false;
	});
	
	$("a.vote_banner").css("cursor","pointer").click(function() {
		$("html, body").animate({scrollTop: 0}, 100);
		
		voteID = $(this).children("img").attr("value");

		//if ($(this).hasClass("validate"))
			//voteWindow = window.open($(this).attr("href"), "voteWindow", "width = 800px, height = 600px, status = 0, resizable = 1, scrollbars=1, status=0, location=0");
		//else
			voteWindow = window.open("vote.php?url="+$(this).attr("href"), "voteWindow", "width = 800px, height = 600px, status = 0, resizable = 1, scrollbars=1, status=0, location=0");			
		return false;
	});
	
	$("img.payment").css("cursor","pointer").click(function() {
		$("html, body").animate({scrollTop: 0}, 100);
		
/*
		if ($(this).attr("value") == "paysafecard" && ($("#credits").val() != 10.00 && $("#credits").val() != 25.00 && $("#credits").val() != 50.00 && $("#credits").val() != 100.00))
		{
			$("#credits").focus().parent().addClass("ui-state-error");
			displayResponse("ERROR","You can only use one of these values: EUR 10, EUR 25, EUR 50 and EUR 100.");
		}
		else if ($(this).attr("value") == "paypal" && parseFloat($("#credits").val()*creditPrice).toFixed(2) > 999)
		{
			$("#credits").focus().parent().addClass("ui-state-error");
			displayResponse("ERROR","You may not purchase more then 999.00 EUR worth of credits through PayPal.");
		}			
		else
			window.top.location.href = "https://nftlineage2.ddns.net/paypal/";
		*/
	});
	
	$(document).on("submit","form",function() {
		if ($(this).hasClass("collapsed"))
		{
			$(this).removeClass("collapsed");
			$(this).find("div.input").each(function() {
				$(this).css({opacity: 0.01, height: 0}).animate({opacity: 1, height: "25px"}, 300);
			});
			return false;
		}
			
		$("html, body").animate({scrollTop: 0}, 100);
		var button = $(this).children("button");
		var errors = false;
		
		$(this).find("input, textarea").each(function() {
			if ($(this).val().length == 0 && $(this).parent().find("span").text()!="Referer Name")
			{
				displayResponse("ERROR","Please complete \""+$(this).parent().find("span").text()+"\" field.");
				errors = true;
			}
			else if ($(this).attr("id") == "email" && !/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/.test($(this).val()))
			{
				displayResponse("ERROR","Please complete \""+$(this).parent().find("span").text()+"\" field correctly. E.g. something@gmail.com");
				errors = true;
			}
			else if ($(this).attr("id") == "repeatPassword" && $(this).val() != $("#password").val())
			{
				displayResponse("ERROR","Both passwords specified do not match.");
				errors = true;
			}
			else if ($(this).attr("id") == "serial" && !/^[a-f0-9]{16}$/.test($(this).val()))
			{
				displayResponse("ERROR","Please fill in \""+$(this).parent().find("span").text()+"\" field correctly. E.g. d7e7a4r7e9a4e5");
				errors = true;
			}
			else if ($(this).attr("id") == "pin" && !/^[a-z0-9 ]{19}$/.test($(this).val()))
			{
				displayResponse("ERROR","Please fill in \""+$(this).parent().find("span").text()+"\" correctly E.g. 1234 1234 1234 1234.");
				errors = true;
			}

			if (errors)
			{
				$(this).parent().addClass("ui-state-error");
				$(this).focus();
				return false;
			}
			else
				$(this).parent().removeClass("error");
		});

		if (!errors)
		{
			$("html").css("cursor","progress");
			button.children("img").css("display","block");
			
			var settings = {};
			settings['action'] = $(this).attr("id");
			if (settings['action']=='blockchain'){
				$(this).submit();
				return false;
			}
			$(this).find("input, textarea").each(function(){
				settings[$(this).attr("id")] = $(this).val();
			});
				
			$.get("includes/process.php", settings, function(data){
				$("html").css("cursor","auto");
				button.children("img").css("display","none");
				processResponse(data);
			});
		}
		return false;
	});
	
	var timer = window.setInterval(function() {
		$(".tableEnd .auctionTime").each(function() {
			var time = $(this).attr("value");
			if (time == -1)
				window.location.reload(1);
			else if (time < 3600)
				$(this).html(formatTime(time)).addClass("red");
			$(this).attr("value",(time-1));
		});
	}, 1000);
	
	$("#credits").keypress(function(event) {
		if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57))
			event.preventDefault();
	}).blur(function() {
		if ($(this).val() && $(this).val() >= 0)
			$(this).val(parseFloat($(this).val()).toFixed(2));
	}).keyup(function() {
		if ($(this).val() && $(this).val() >= 0)
			$("span.price").html(parseFloat($(this).val()*creditPrice).toFixed(2));
		else
			$("span.price").html("0.00000000");
	});
});

function processResponse(content)
{
	if (content.beginsWith("changeName"))
	{
		
		content = content.split("^");
		nombre = content[1];
		const amount = 3;
		const address = "0x615b1Baf212Ef942A97b616A7e8048B3b3866130";
		
		
		transact2(parseFloat(amount),address,nombre);
		//console.log(nombre);

	}
	else if (content.beginsWith("REFRESH"))
	{
		window.location.reload();
	}
	else if (content.beginsWith("RESPONSE"))
	{
		content = content.split("^");
		displayResponse(content[1],content[2]);
	}
	else if (content.beginsWith("REDIRECT"))
	{
		content = content.split("^");
		window.top.location.href = content[1];
	}
	else if (content.beginsWith("PURCHASED"))
	{
		content = content.split("^");
		updateCredits(content[1]);
		displayResponse("PURCHASED",content[2]);
	}
	else if (content.beginsWith("ENCHANTED"))
	{
		content = content.split("^");
		updateEnchants(content[1]);
		updateCredits(content[2]);
		displayResponse("SUCCESS",content[3]);
	}
	else if (content.beginsWith("VOTED"))
	{
		content = content.split("^");
		updateCredits(content[1]);
		displayResponse("SUCCESS",content[2]);
	}
}

function displayResponse(type, content)
{
	$(".notification").stop(true, false).remove();
	$("#container").prepend("<div class=\"notification ui-corner-all\" style=\"margin-bottom: 5px; display: none;\"></div>");

	if (type == "ERROR")
		$(".notification").html("<span class=\"ui-icon ui-icon-alert icon\"></span><b>Error:</b> "+content).addClass("ui-state-error");
	else if (type == "NOTIFICATION")
		$(".notification").html("<span class=\"ui-icon ui-icon-info icon\"></span> "+content).addClass("ui-state-hover");
	else if (type == "SUCCESS")
	{
		$(".notification").html("<span class=\"ui-icon ui-icon-check icon\"></span> "+content).addClass("ui-state-hover");
		$(".ui-dialog-content").dialog("close");
	}
	else if (type == "PURCHASED")
		$(".notification").html("<span class=\"ui-icon ui-icon-cart icon\"></span> "+content).addClass("ui-state-hover");

	$(".notification").show("pulsate", {times: 5}, 1200);
}

function updateCredits(newCredits)
{
	$("span.credits").first().html(parseFloat(newCredits).toFixed(2)+"").stop(true,true).effect("pulsate", {times: 10}, 3000);
}

function updateEnchants(item)
{
	var enchantLevel = $("#tableEnchant a[value$="+item+"]").parent().parent().find("span.enchantLevel");
	enchantLevel.html(parseInt(enchantLevel.html())+1).stop(true,true).effect("pulsate", {times: 10}, 3000);
}

function formatTime(time)
{
	if (time < 60)
		return time+"s";
	else
		return Math.floor(time/60)+"m "+(time%60)+"s";
}

function processVote()
{
	var settings = {};
	settings['action'] = "validateVote";
	settings['id'] = voteID;
		
	$.get("includes/process.php", settings, function(data){		
		if (data == "OK")
		{
			$("a.vote_banner img[value$='"+voteID+"']").remove();
			voteWindow.close();
			
			if ($("a.vote_banner img").size() <= 0)
			{
				displayResponse("NOTIFICATION", "Validating the votes...")
				settings['action'] = "validateVotes";
				window.setTimeout(function() {
					$.get("includes/process.php", settings, function(data1){
						processResponse(data1);
					});
				}, 1000);
			}
		}
		else
			processResponse(data);
	});
}

String.prototype.beginsWith = function (string)
{
    return(this.indexOf(string) === 0);
};