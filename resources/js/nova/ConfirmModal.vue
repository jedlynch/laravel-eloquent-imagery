<template>
  <portal to="modals" v-if="showModal">
    <modal @modal-close="handleClose">
      <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-8">
          <heading :level="2" class="mb-6">{{modal.header}}</heading>
          <p class="text-80">
            {{modal.message}}
          </p>
        </div>
        <div class="bg-30 px-6 py-3 flex">
          <div class="ml-auto">
            <button dusk="cancel-upload-delete-button"
                    type="button"
                    data-testid="cancel-button"
                    @click.prevent="handleClose"
                    class="btn text-80 font-normal h-9 px-3 mr-3 btn-link"
            >
              {{ __('Cancel') }}
            </button>

            <button v-if="modal.showConfirm"
                    dusk="confirm-upload-delete-button"
                    ref="confirmButton"
                    data-testid="confirm-button"
                    @click.prevent="handleConfirm"
                    class="btn btn-default btn-danger"
            >
              {{ __('Upload') }}
            </button>
          </div>
        </div>
      </div>
    </modal>
  </portal>
</template>

<script>
  import ImageCardDisplay from './ImageCardDisplay'

  export default {
    props: [
      'file',
      'metadata',
      'resourceId',
      'field'
    ],

  }
</script>
