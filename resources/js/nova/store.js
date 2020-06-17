
export default {
  namespaced: true,

  state: {
    fieldName: '',
    images: [],
    isCollection: false
  },

  mutations: {
    initialize(state, payload) {
      state.fieldName = payload.fieldName
      state.images = payload.images
      state.isCollection = payload.isCollection
    },

    updateImages(state, images) {
      state.images = images
    }
  },

  actions: {
    addImageFromFile ({ state, commit }, payload) {
      let file = payload.file

      let imageUrl = URL.createObjectURL(file)

      let metadata = payload.hasOwnProperty('metadata') ? payload.metadata : []

      let image = {
        inputId: 'eloquent-imagery-' + state.fieldName + '-' + (state.images.length + 1),
        previewUrl: imageUrl,
        thumbnailUrl: imageUrl,
        metadata: Object.keys(metadata).map(key => ({'key': key, 'value': metadata[key]})),
      }

      let images = state.images
      images.push(image)

      commit('updateImages', images)

      return new Promise((resolve, reject) => {
        let reader = new FileReader()

        reader.addEventListener('load', () => {
          image.fileData = reader.result

          resolve(image)
        })

        reader.readAsDataURL(file)
      })
    },

    addImage ({ state, commit }, image) {



      switch(true){
        case (['jpg','png','gif'].indexOf(fileType) == -1):
          this.renderModal(
              'A '+ fileType +' image is unsupported.',
              'An image must be in a .jpg, .png, or .gif format.',
              false
          );

          break;

        case (this.field.maximumSize && file.size > this.field.maximumSize):
          this.fileSize = file.size;
          this.renderModal(
              'Are you sure you want to upload this image?',
              'Warning image is ' + this.fileSizeFormatted(),
              true
          );

          break;


      let images = state.images
      images.push(image)

      commit('updateImages', images)
    },

    removeImage ({ state, commit }, imageToRemove) {
      commit('updateImages', state.images.filter(image => image !== imageToRemove))
    }
  },

  getters: {
    getImages: (state) => state.images,
    getIsCollection: (state) => state.isCollection
  }
}
