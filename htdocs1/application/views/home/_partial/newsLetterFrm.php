        <section class="section footer-head">
            <div class="container container--fixed">
                <div class="footer-subscribe">
                    <div class="section-title">
                        <h2><?php echo Label::getLabel("LBL_We'd_love_to_send_you_our_Newsletter", $siteLangId); ?></h2>
                    </div>
                    <?php
                        $newsLetterForm->setFormTagAttribute("onsubmit","setUpNewsLetter(this); return false;");
                        $newsLetterForm->setFormTagAttribute("class","form footer-subscribeForm");
                        echo $newsLetterForm->getFormTag();
                        $email = $newsLetterForm->getField('email');
                        $email->addFieldTagAttribute("placeholder",Label::getLabel('LBL_Enter_Your_Email', $siteLangId));
                        echo $newsLetterForm->getFieldHTML('email');
                     ?>
                    </form>
                </div>
            </div>
        </section>


<script>
    (function() {
        setUpNewsLetter = function(frm) {
            var data = fcom.frmData(frm);
            fcom.updateWithAjax(fcom.makeUrl('MyApp', 'setUpNewsLetter'), data, function(t) {
                if(t.status){
                    document.frmNewsLetter.reset();
                }
            });
        };
    })();
</script>
