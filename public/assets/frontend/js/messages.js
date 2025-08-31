$(function() {

    $('#message').on('keypress', function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
           sendTxtMessage($(this).val(), 'Text');
        }
    });

    $('#btnSend1').on('click', function(){
           sendTxtMessage($('#message').val(), 'Text');
    });

    $('#btnSend2').on('click', function(){
           sendTxtMessage($('#message').val(), 'Text');
    });

    var gif = document.getElementById("sendGIFButton");
    var gifmodal = document.getElementById('gifModal');
    $('#sendGIFButton').on('click', function(){
        document.getElementById('gifModal').style.display = "block";
    });

    //GIFs
    $("#gifInput").on('keyup', function(){
        var xhr = $.get("https://api.giphy.com/v1/gifs/search?q=" + $(this).val() +"&api_key=dNhCbN6hrhpBMxXhIswM34wIR2UBpCns&limit=30");
        xhr.done(function(response) {
            //console.log("success got data", response);
            let embed = "";
            let gif;
            $("#listGifs").empty();
            for(let i = 0; i < response.data.length; i++){
                embed = response.data[i]["images"]["downsized"]["url"];
                gif = $("<img class='giffy mb-2' src='" + embed + "' width='335' height = '280'/>");
                gif.click(function(){
                    sendTxtMessage("<img class='giffy mb-2' src='" + $(this).attr("src") + "' width='335' height = '280'/>", 'Gif');
                });
                $("#listGifs").append(gif);
            }

            if(response.data.length == 0){
                $("#listGifs").append("<p class='no-gif'>No Matching GIFs Found </p>");
            }
        });

    });

    //Make modals disappear
    window.onclick = function(event) {
        if (event.target == gifmodal) {
            gifmodal.style.display = "none";
        }
    }



});	///end of jquery


function ScrollDown(){
    var ChatDiv = $('#messagesContent');
    var height = ChatDiv[0].scrollHeight;
    ChatDiv.animate({scrollTop: height}, 1000);
}
window.onload = ScrollDown();


$("#searchUser").on('keyup',function(){

	var search_term = $('#searchUser').val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: APP_URL + "/user/chats/user",
        data: {'search_term': search_term},
        success: function(response) {

            $('#searchUsersBox').html(response);

        }
    });

});

$("#searchChats").on('keyup',function(){

	var search_term = $('#searchChats').val();

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: APP_URL + "/user/chats/get",
        data: {'search_term': search_term},
        success: function(response) {
            $('#sidebarChats').html(response);
        }
    });

});

function create_chat(id){

	var user_id = id;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: APP_URL + "/user/chats/new",
        data: {'user_id': user_id},
        success: function(response) {

            if (response.status == 200) {

                tata.success("Success", response.messages, {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });

                window.location = APP_URL + "/user/chats/"+response.chat_id+"/messages";

            }else if(response.status == 401){

                tata.error("Error", response.messages, {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });

                window.location.reload();

            }else if(response.status == 402){

                tata.error("Error", response.messages, {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });

                window.location = APP_URL + "/user/chats";
            }

        }
    });

}

function DisplayMessage(message, type, APP_URL){
	var Sender_Name = $('#Sender_Name').val();
	var Sender_ProfilePic = $('#Sender_ProfilePic').val();
	var time = $('#Current_Time').val();

		var str = '<div class="d-flex align-items-start justify-content-end mb-3 chat-box">';
				str+='<div class="pe-2 me-1 chat-message">';
				  str+='<div class="bg-green text-light p-3 mb-1 chat-text-right">';

                    if (type === 'Text' || type === 'Gif') {

                      str+='<p>'+message+'</p>' ;

                    }else if (type === 'Image') {

                      str+='<img src="'+APP_URL+'/public/uploads/attachments/'+message+'" class="img-fluid">';

                    } else if(type === 'Video') {

                      str+='<video width="320" height="240" controls>';
                      str+='<source src="'+APP_URL+'/public/uploads/attachments/'+message+'" type="video/mp4">';
                      str+='<source src="'+APP_URL+'/public/uploads/attachments/'+message+'" type="video/ogg">';
                      str+='Your browser does not support the video tag.';
                      str+='</video>';

                    } else if(type === 'Audio') {

                      str+='<audio controls>';
                      str+='<source src="'+APP_URL+'/public/uploads/attachments/'+message+'" type="audio/ogg">';
                      str+='<source src="'+APP_URL+'/public/uploads/attachments/'+message+'" type="audio/mpeg">';
                      str+='Your browser does not support the audio element..';
                      str+='</audio>';

                    } else if(type === 'Zip') {

                      str+='<p>Zip File</p>' ;
                      str+='<a download href="'+APP_URL+'/public/uploads/attachments/'+message+'" class="btn btn-danger btn-sm">Download</a>';

                    }

				  str+='</div>'
				  str+='<div class="d-flex justify-content-end align-items-center small text-muted">'+time+'<i class="bi bi-check-all text-muted ms-2"></i></div>';
				str+='</div>'
				str+='<img src="'+APP_URL+'/public/uploads/users/'+Sender_ProfilePic+'" class="rounded-circle" width="40" alt="'+Sender_Name+'">';
				 str+='</div>';
		$('#dumppy').append(str);
}

function sendTxtMessage(message, type){
	var messageTxt = message;
	var fileType = type;
	if(messageTxt!=''){

        DisplayMessage(messageTxt, type, APP_URL);
        ScrollDown();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var chat_id = $('#Chat_ID').val();
        $.ajax({
            dataType : "json",
            type : 'post',
            data : {messageTxt : messageTxt, chat_id : chat_id , fileType : fileType},
            url: APP_URL + "/user/chats/messages/send",
            success:function(response)
            {
                $('#message').text('');
            }
        });

		$('#message').focus();
	}else{

        tata.error("Error", 'Message cannot be Empty', {
        position: 'tr',
        duration: 3000,
        animate: 'slide'
        });

	}
}


$('#uploadFile').on('change', function(){


    var file_data = $('#uploadFile').prop('files')[0];
    var chat_id = $('#Chat_ID').val();
    var fileType = 'Attachment';
    var form_data = new FormData();
    form_data.append('attachmentfile', file_data);
    form_data.append('fileType', fileType);
    form_data.append('chat_id', chat_id);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: APP_URL + "/user/chats/messages/upload",
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(response){

            if (response.status == 200) {

                $('#uploadFile').val('');
                DisplayMessage(response.file_name, response.type, APP_URL);
                ScrollDown();

            } else if(response.status == 400){

                tata.error("Error", response.messages, {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });
            }

        }
     });

});


$('#zipFile').on('change', function(){

    //DisplayMessage('<div class="spiner"><i class="lni lni-circle-o-notch lni-spin"></i></div>');
    //ScrollDown();

    var file_data = $('#zipFile').prop('files')[0];
    var chat_id = $('#Chat_ID').val();
    var fileType = 'Attachment';
    var form_data = new FormData();
    form_data.append('attachmentfile', file_data);
    form_data.append('fileType', fileType);
    form_data.append('chat_id', chat_id);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: APP_URL + "/user/chats/messages/zip",
        dataType: 'json',
        cache: false,
        contentType: false,
        processData: false,
        data: form_data,
        type: 'post',
        success: function(response){

            if (response.status == 200) {

                $('#zipFile').val('');
                DisplayMessage(response.file_name, response.type, APP_URL);
                ScrollDown();

            } else if(response.status == 400){

                tata.error("Error", response.messages, {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });
            }

        }
     });

});


function mute_chat(chat_id, type){

	var chat_id = chat_id;
	var type = type;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        url: APP_URL + "/user/chats/mute",
        data: {'chat_id': chat_id,'type': type},
        success: function(response) {

            if (response.status == 200) {

                tata.success("Success", response.messages, {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });

                window.location.reload();

            }else if(response.status == 401){

                tata.error("Error", response.messages, {
                position: 'tr',
                duration: 3000,
                animate: 'slide'
                });

                window.location.reload();

            }

        }
    });

}

function delete_chat(id, message) {

    Swal.fire({
        title: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1cbb8c',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed)
        {
            start_load();

            var data = {
                'id': id,
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                method: "POST",
                url: APP_URL + "/user/chats/delete",
                data: data,
                success: function (response) {
                    end_load();
                    window.location = APP_URL + "/user/chats";
                    Swal.fire('Deleted!',response.status,'success');
                }
            });
        }

    });
}
