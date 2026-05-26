import ModalActionResponse from './components/ModalActionResponse'
import BlockText from './components/BlockText'

Nova.booting(app => {
    app.component('modal-response', ModalActionResponse)
    app.component('block-text', BlockText)
});
