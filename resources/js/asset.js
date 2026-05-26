import ModalActionResponse from './components/ModalActionResponse'
import TextBlock from './components/TextBlock'

Nova.booting(app => {
    app.component('modal-response', ModalActionResponse)
    app.component('modal-response-text-block', TextBlock)
});
