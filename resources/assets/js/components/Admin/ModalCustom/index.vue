<template>
    <div class="modal fade" id="modalConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalConfirm"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="contentModalConfirm">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-example" v-on:click="acceptModal()">Yes</button>
            </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        title: {
            type: String,
            default: null,
            required: true,
        },
        content: {
            type: String,
            defaule: null,
            required: true,
        },
        accept: {
            type: Function,
            required: false,
        },
        cancel: {
            type: Function,
            required: false,
        },
        isOpen: {
            type: Boolean,
            default: false,
        }
    },

    data () {
        return {
            titleData: this.title,
            contentData: this.content
        };
    },

    methods: {
        acceptModal () {
            this.closeModal();
            if(this.accept) {
                this.accept('test');
            }
            
        },

        cancelModal () {
            this.closeModal();
        },

        closeModal () {
            $('#modalConfirm').modal('hide');
        }
    },

    mounted () {
        $('#modalConfirm').on('hide.bs.modal', function (e) {
            debugger
        });
    },

    watch: {
        isOpen: function(newVal, oldVal) {
            if(!!newVal) {
                $("#titleModalConfirm").html(this.titleData);
                $("#contentModalConfirm").html(this.contentData);

                $('#modalConfirm').modal({
                    "backdrop" : "static",
                    "show": true
                });
            }
        }
    }
}
</script>
