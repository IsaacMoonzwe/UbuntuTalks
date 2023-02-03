$(document).ready(function(){
    agendaSetupTestimonial = function (frm){
		var data = fcom.frmData(frm);
		console.log("data",data);
		data=data+'&start_time='+startTime+'&end_Time='+endTime;
		fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'agendasetup'), data, function(t) {		
            location.reload(true);
			reloadList();
			if (t.langId>0) {
				editTestimonialLangForm(t.testimonialId, t.langId);
				return ;
			}	
			$(document).trigger('close.facebox');
		});
	}
  });