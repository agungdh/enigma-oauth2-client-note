import axios from "axios"
import { Modal } from "flowbite";

const createModal = new Modal(document.getElementById('createModal'));
const deleteModal = new Modal(document.getElementById('deleteModal'));

export default {
    datas: [],
    id: '',

    form: {
        title: { value: '', validation: '' },
        note: { value: '', validation: '' },
    },

    init() {
        this.getNoteDatas()
    },

    formValue() {
        return {
            title: this.form.title.value,
            note: this.form.note.value,
        }
    },

    async getNoteDatas() {
        let res = await axios.get('/note')
        let datas = res.data

        this.datas = datas
    },

    async submit() {
        try {
            await axios.post('/note', this.formValue())

            this.getNoteDatas()

            this.hideCreateModal()
        } catch (error) {
            if (error.status == 422) {
                let errors = error.response.data.errors

                this.form.title.validation = errors.title ? errors.title[0] : ''
                this.form.note.validation = errors.note ? errors.note[0] : ''
            } else {
                throw error
            }
        }
    },

    async deleteData() {
        try {
            await axios.delete(`/note/${this.id}`)

            this.getNoteDatas()

            this.hideDeleteModal()
        } catch (error) {
            throw error
        }
    },

    openCreateModal() {
        this.id = ''

        this.form = {
            title: { value: '', validation: '' },
            note: { value: '', validation: '' },
        }

        createModal.show()
    },

    hideCreateModal() {
        createModal.hide()
    },

    openDeleteModal(id) {
        this.id = id

        deleteModal.show()
    },

    hideDeleteModal() {
        deleteModal.hide()
    },
}