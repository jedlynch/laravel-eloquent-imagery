<template>
  <default-field :field="field" :errors="errors" :full-width-content="true">
    <template slot="field">
      <div class="bg-white rounded-lg">
        <draggable v-model="images" group="image-group" v-on:start="drag=true" v-on:end="drag=false" :class="`flex flex-wrap mb-2 laravel-eloquent-imagery-${this.resourceName}`">
          <div v-for="(image, index) in images" :class="`pl-1 pr-1 border border-70 flex items-end m-1 laravel-eloquent-imagery-image-${(index + 1)}`">
              <image-card-input v-bind:image.sync="image" v-on:remove-image="removeImage"></image-card-input>
          </div>

          <!--<button v-on:click.prevent="debugThis">De</button>-->

          <div v-if="(isCollection == false && images.length == 0) || isCollection" slot="footer" class="flex items-center pl-1 pr-1 m-1 border border-70">
            <div class="content-center px-6 py-4">
              <input
                ref="addNewImageFileInput"
                class="select-none form-file-input"
                type="file"
                v-bind:id="`eloquent-imagery-` + this.field.name + `-add-image`"
                name="name"
                v-on:change="addImage"
              />

              <span v-on:click="() => this.$refs['addNewImageFileInput'].click()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="72" width="72">
                  <path fill="#888" d="M6 2h9a1 1 0 0 1 .7.3l4 4a1 1 0 0 1 .3.7v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4c0-1.1.9-2 2-2zm9 2.41V7h2.59L15 4.41zM18 9h-3a2 2 0 0 1-2-2V4H6v16h12V9zm-5 4h2a1 1 0 0 1 0 2h-2v2a1 1 0 0 1-2 0v-2H9a1 1 0 0 1 0-2h2v-2a1 1 0 0 1 2 0v2z"/>
                </svg>
              </span>
            </div>
          </div>
        </draggable>

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
                          v-bind:value="false"
                          @click.prevent="$emit('close', $event.target.value)"
                          class="btn text-80 font-normal h-9 px-3 mr-3 btn-link"
                  >
                    {{ __('Cancel') }}
                  </button>

                  <button v-if="modal.showConfirm"
                          dusk="confirm-upload-delete-button"
                          ref="confirmButton"
                          data-testid="confirm-button"
                          v-bind:value="true"
                          @click.prevent="$emit('close', $event.target.value)"
                          class="btn btn-default btn-danger"
                  >
                    {{ __('Upload') }}
                  </button>
                </div>
              </div>
            </div>
          </modal>
        </portal>

      </div>
    </template>
  </default-field>
</template>

<script>
  import { FormField, HandlesValidationErrors, Errors } from 'laravel-nova'
  import Draggable from 'vuedraggable'
  import ImageCardInput from './ImageCardInput'

  import store from './store'

  export default {
    mixins: [FormField, HandlesValidationErrors],

    props: ['resourceName', 'resourceId', 'field'],

    components: {
      ImageCardInput,
      Draggable
    },
    data () {
      return {
        modal: {
          'header': '',
          'message': '',
          'showConfirm': false
        },
        showModal: false,
        modalOption: false
      }
    },
    computed: {
      images: {
        get () {
          return this.$store.getters[`eloquentImagery/${this.field.name}/getImages`]
        },
        set (value) {
          this.$store.commit(`eloquentImagery/${this.field.name}/updateImages`, value)
        }
      },

      isCollection () {
        return this.$store.getters[`eloquentImagery/${this.field.name}/getIsCollection`]
      }
    },

    methods: {
      debugThis () {
        console.log(this.images)
      },

      setInitialValue () {
        let isCollection = this.field.isCollection

        let images = (isCollection ? this.field.value.images : (this.field.value ? [this.field.value] : []))
          .map((image, i) => {
            return {
              inputId: 'eloquent-imagery-' + this.field.name + '-' + i,
              previewUrl: image.previewUrl,
              thumbnailUrl: image.thumbnailUrl,
              path: image.path,
              metadata: Object.keys(image.metadata).map(key => ({'key': key, 'value': image.metadata[key]}))
            }
          })

        this.$store.commit(`eloquentImagery/${this.field.name}/initialize`, { fieldName: this.field.name, isCollection, images })
      },

      addImage (event, metadata = {}) {
        let file = event.target.files[0]
        let fileType = file.type.replace('image/','');
        let imageUrl = URL.createObjectURL(file)

        let image = {
          inputId: 'eloquent-imagery-' + this.field.name + '-' + (this.images.length + 1),
          previewUrl: imageUrl,
          thumbnailUrl: imageUrl,
          metadata: Object.keys(metadata).map(key => ({'key': key, 'value': metadata[key]}))
        }

        let modalPromise;
        switch (true) {
          case (['jpg', 'jpeg', 'png', 'gif'].indexOf(fileType) == -1):
            modalPromise = this.renderModal(
                    'A ' + fileType + ' image is unsupported.',
                    'An image must be in a .jpg, .png, or .gif format.',
                    false
            );
            break;
          case (this.field.maximumSize && file.size > this.field.maximumSize):
            modalPromise = this.renderModal(
                    'Are you sure you want to upload this image?',
                    'Warning image is ' + this.fileSizeFormatted(file),
                    true
            );
            break;

          default:
            modalPromise = new Promise((resolve) => {
              resolve;
            });
        }

        let object = this;
        modalPromise.then((shouldLoadImage)=>{
          if(!shouldLoadImage){
            object.removeImage(image);
            return;
          }
          object.$store.dispatch(`eloquentImagery/${object.field.name}/addImage`, image);
          return new Promise((resolve, reject) => {
            let reader = new FileReader()
            reader.addEventListener('load', () => {
              image.fileData = reader.result
              resolve(image)
            })
            reader.readAsDataURL(file)
          })
        })
      },

      renderModal(header,message,showConfirm,callback){
        this.modal = {
          'header': header,
          'message': message,
          'showConfirm': showConfirm
        };

        let object = this;
        return new Promise((resolve, reject) => {
          object.showModal = true;
          object.$on('close', (modalOption) => {
            object.showModal = false;
            resolve(modalOption)
          })
        });
      },

      removeImage (image) {
        this.$store.dispatch(`eloquentImagery/${this.field.name}/removeImage`, image)
        this.$refs['addNewImageFileInput'].value = null;
      },

      fileSizeFormatted(file) {
        if(!file){
          return;
        }
        let fileSize = file.size;
        switch(true){
          case (fileSize/1000000 > 1):
            return Math.round(fileSize/1000000) +'MB';
          case (fileSize/1000 > 1):
            return Math.round(fileSize/1000) +'KB';
          default:
            return fileSize;
        }
      },

      fill (formData) {
        let serializedImages = this.images.map(image => ({
          fileData: (image.hasOwnProperty('fileData') ? image.fileData : null),

          path: (image.hasOwnProperty('path') ? image.path : null),

          metadata: image.metadata.reduce((object, next) => {
            object[next.key] = next.value
            return object
          }, {})
        }))

        formData.append(this.field.attribute, JSON.stringify(this.isCollection ? serializedImages : serializedImages.pop()))
      }
    },

    created () {
      this.$store.registerModule(`eloquentImagery/${this.field.name}`, store)
    },

    destroyed () {
      this.$store.unregisterModule(`eloquentImagery/${this.field.name}`)
    }
  }
</script>
