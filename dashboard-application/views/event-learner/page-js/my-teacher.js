$(function() {
	var dv = '#gt-data';
	localStorage.removeItem("tid");
	searchLessons = function(tid,page){
		if(typeof page == undefined || page == null){
			page = 1;
		}
		$(dv).html(fcom.getLoader());		
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','search'),'teacherId='+tid+'&page='+page,function(t){
			$(dv).html(t);
		});
		localStorage.tid = tid;
	};	

	searchFlashCards = function(tid,page){
		if(typeof page == undefined || page == null){
			page = 1;
		}		
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','searchFlashCards'),'teacherId='+tid+'&page='+page,function(t){
			$(dv).html(t);
		});
		localStorage.tid = tid;
	};	

	viewFlashCard = function(flashcardId){	
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','viewFlashCard',[flashcardId]),'',function(t){
			$.facebox( t,'facebox-medium');
		}); 
	};

	addFlashcard = function(flashcardId,learnerId,teacherId,lessonId){	
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','addFlashcard'),'flashcardId='+flashcardId+'&learnerId='+learnerId+'&teacherId='+teacherId+'&lessonId='+lessonId,function(t){
			$.facebox( t,'facebox-medium');
		}); 
	};	
	
	removeFlashcard = function(id){	
		if(confirm(langLbl.confirmRemove))
		{
			fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons','removeFlashcard',[id]),'',function(t){
				location.reload();
			}); 
		}
	};	
	
	setupFlashcard = function(frm,learnerId,teacherId){
		if (!$(frm).validate()) return false;
		var data = fcom.frmData(frm);
	 fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'setupFlashcard'), data+"&learnerId="+learnerId+"&teacherId="+teacherId , function(t) {		
				searchFlashCards( frmFlashCardSrch );
				$.facebox.close();
		});	 
	}	

	goToFlashCardSearchPage = function(page) {
		if(typeof page == undefined || page == null){
			page = 1;
		}
		searchFlashCards(localStorage.getItem("tid"),page);
	};

	goToSearchPage = function(page) {
		if(typeof page == undefined || page == null){
			page = 1;
		}		
		searchLessons(localStorage.getItem("tid"),page);
	};	

	requestReschedule = function(id){	
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','requestReschedule',[id]),'',function(t){
			searchLessons(document.frmSrch);
			$.facebox( t,'facebox-medium');
		}); 
	};
	
	requestRescheduleSetup = function(frm){	
	if (!$(frm).validate()) return;	
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'requestRescheduleSetup'), data , function(t) {		
				$.facebox.close();				
				location.reload();
		});	
	};	

	cancelLesson = function(id){	
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('EventLearnerScheduledLessons','cancelLesson',[id]),'',function(t){
			searchLessons(document.frmSrch);
			$.facebox( t,'facebox-medium');
		}); 
	};
	
	cancelLessonSetup = function(frm){	
	if (!$(frm).validate()) return;	
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('EventLearnerScheduledLessons', 'cancelLessonSetup'), data , function(t) {		
				$.facebox.close();
				location.reload();
		});	
	};	
	
	
});