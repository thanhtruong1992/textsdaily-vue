<template src="../template/index.html">
    
</template>
<script>
import ClientApi from '../../../../api/client';
import ModalCustom from '../../ModalCustom/index.vue';
import Notification from '../../../../notify/index';

export default {
    components: {
        'modal-custom': ModalCustom
    },
    data () {
        var data = {
            clients: null,
            currentUser: JSON.parse(localStorage.getItem('auth')),
            modalCustom: {
                isOpen: false,
                titleModal: null,
                contentModal: null,
            }
        }
        // get client
        ClientApi.getAll()
            .then(res => {
                data.clients = res;
            })
            .catch(err => {
                // error
            });
        return data;
    },

    methods: {
        openModal (key) {
            var listChecked = $('.frmClientCheck:checked');
            if(listChecked.length > 0) {
                switch (key) {
                    case 'delete':
                        this.modalCustom.titleModal = 'Delete Client';
                        this.modalCustom.contentModal = 'Are you sure to delete the selected clients?';
                    break;
                    case 'enable':
                        this.modalCustom.titleModal = 'Delete Client';
                        this.modalCustom.contentModal = 'Are you sure to enable the selected clients?';
                    break;
                    case 'disable':
                        this.modalCustom.titleModal = 'Delete Client';
                        this.modalCustom.contentModal = 'Are you sure to disable the selected clients?';
                    break;
                    default:
                        this.modalCustom.titleModal = 'Delete Client';
                        this.modalCustom.contentModal = 'Are you sure to delete the selected clients?';
                    break;
                }
            }
            
            var listChecked = $('.frmClientCheck:checked');
            if(listChecked.length > 0) {
                this.isDelete = true;
            }
        },

        actionEnable () {
            var listChecked = $('.frmClientCheck:checked');
            if(listChecked.length > 0) {
                this.isEnable = true;
            }
        },

        actionDisable () {
            var listChecked = $('.frmClientCheck:checked');
            if(listChecked.length > 0) {
                this.isDisable = true;
            }
        },

        deleteClient (res) {
            var listClientIDSelected = this.getClientWasChecked();
            ClientApi.destroyMultiple({
                    list_id: listClientIDSelected
                })
                .then(res => {
                    Notification.show('success', {
                        content: res.message
                    });
                })
                .catch(err => {
                    Notification.show('error', {
                        content: res.message
                    });
                });
            this.isDelete = false;
        },

        enableClient () {
            var listClientIDSelected = this.getClientWasChecked();

            this.isEnable = false;
        },

        disableClient () {
            var listClientIDSelected = this.getClientWasChecked();
        },

        getClientWasChecked() {
            var listChecked = $('.frmClientCheck:checked'); // list checked
            var listClientIDSelected = [];
            $.each(listChecked, function(index, value) {
                if ($(this).val() == "checkAll") {
                    return true; // continue
                }
                listClientIDSelected.push($(this).val());
            });
            return listClientIDSelected;
        },
    },

    mounted () {
        $('#frmClientCheckAll').on('click', function() {
		    $(':checkbox.frmClientCheck').prop('checked', this.checked);
	    });
    }
}
</script>
