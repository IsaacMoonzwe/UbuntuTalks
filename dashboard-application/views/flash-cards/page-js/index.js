var isRuningFlashCardAjax = false;
$(function() {
	var dv = '#listItems';
	count = 1;
	currentReviewedCount = 0;
	allFCardCounts = 0;

	searchFlashCards = function(frm){
		$(dv).html(fcom.getLoader());
		var data = fcom.frmData(frm);
		fcom.ajax(fcom.makeUrl('FlashCards','search'),data,function(t){
			$(dv).html(t);
		});
	};

	goToSearchPage = function(page) {
		if(typeof page == undefined || page == null){
			page = 1;
		}
		var frm = document.frmFlashCardSearchPaging;
		$(frm.page).val(page);
		searchFlashCards(frm);
	};

	clearSearch = function(){
		document.frmSrch.reset();
		searchFlashCards( document.frmSrch );
	}

	flashCardForm = function(id){
		fcom.ajax(fcom.makeUrl('FlashCards','form',[id]),'',function(t){
			$.facebox( t,'facebox-medium');
		});
	};

	setUp = function(frm){
		if (!$(frm).validate()) return false;

		if(isRuningFlashCardAjax) {
			return false;
		}
		isRuningFlashCardAjax = true;

		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('FlashCards', 'setUp'), data , function(t) {
			isRuningFlashCardAjax = false;
			searchFlashCards(document.frmSrch);
			loadFlashCardReviewSection()
			$.facebox.close();
		});
	};

	remove = function(id){
        $.confirm({
            title: langLbl.Confirm,
            content: langLbl.confirmRemove,
            buttons: {
                Proceed: {
                    text: langLbl.Proceed,
                    btnClass: 'btn btn--primary',
                    keys: ['enter', 'shift'],
                    action: function(){
                        fcom.updateWithAjax(fcom.makeUrl('FlashCards','remove',[id]),'',function(t){
                            searchFlashCards(document.frmFlashCardSearchPaging);
                            loadFlashCardReviewSection();
                        });
                    }
                },
                Quit: {
                    text: langLbl.Quit,
                    btnClass: 'btn btn--secondary',
                    keys: ['enter', 'shift'],
                    action: function(){
                    }
                }
            }
        });
	};

	loadFlashCardReviewSection = function(){
		$("#flashCardReviewSection").html( fcom.getLoader() );

		fcom.ajax( fcom.makeUrl('FlashCards','viewFlashCardReviewSection'), '', function(t){
			$("#flashCardReviewSection").html( t );
		});
	}

	reviewFlashCard = function(){
		var data = 'currentReviewedCount='+currentReviewedCount;
		fcom.ajax(fcom.makeUrl('FlashCards','reviewFlashCard'), data, function(t){
			$.facebox( t,'facebox-medium');
		});
	};

	setUpFlashCardReview = function( flashcard_id, flashcard_accuracy ){
		var data = "flashcard_id="+flashcard_id+"&flashcard_accuracy="+flashcard_accuracy;
		fcom.updateWithAjax(fcom.makeUrl('FlashCards','setUpFlashCardReview'),data,function(t){
			//count++;
			loadFlashCardReviewSection();
			if( currentReviewedCount == allFCardCounts ){
				currentReviewedCount = 0;
				reviewResult();
			} else {
				reviewFlashCard();
			}
			//searchFlashCards(document.frmSrch);
			//loadFlashCardReviewSection();

			/* if(t.id!=null)
			{
			$("#CountUnReviewed").html(t.CountUnReviewed);
			reviewFlashCard(t.id)
			}
			else{
			$("#CountUnReviewed").html(t.CountUnReviewed);
			reviewResult();
			count = 1;
			} */
		});
	};

	reviewResult = function(){
		//$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('FlashCards','reviewResult'),'',function(t){
			//searchFlashCards(document.frmSrch);
			$.facebox( t,'facebox-medium');
		});
	};

	searchFlashCards(document.frmSrch);
	loadFlashCardReviewSection();

	/* $(document).bind('close.facebox', function() {
		loadFlashCardReviewSection();
		count = 1;
	}); */
});
