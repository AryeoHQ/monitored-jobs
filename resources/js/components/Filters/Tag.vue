<template>
  <label class="flex items-center space-x-2">
    <input type="checkbox" v-model="selected" />
    <span>{{ tag }}</span>
  </label>
</template>

<script>
import { usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import { ref, watch } from 'vue';

export default {
  props: {
    tag: {
      type: String,
      required: true
    }
  },

  setup(props) {
    const selected = ref(false)
    const loading = ref(false)

    const page = usePage()

    watch(selected, (isSelected) => {
      handleTagChange(isSelected)
    });

    function handleTagChange(isSelected) {
      loading.value = true

      const newTags = [...page.props.value.input?.tags ?? []]
      if (isSelected) {
        newTags.push(props.tag)
      } else {
        newTags.splice(newTags.indexOf(props.tag), 1)
      }

      Inertia.get('/monitored-jobs', {
        ...page.props.value.input,
        tags: newTags
      }, {
        preserveState: true,
        onFinish: () => loading.value = false
      })
    }

    return {
      loading,
      selected
    }
  }
}
</script>