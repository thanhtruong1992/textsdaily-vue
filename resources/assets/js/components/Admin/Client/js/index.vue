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
            isOpen: false,
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
        actionDelete () {
            this.isOpen = true;
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
            this.isOpen = false;
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
