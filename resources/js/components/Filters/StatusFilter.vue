<template>
  <div class="space-y-2">
    <h3>Status</h3>

    <select class="w-full" v-model="selectedStatus">
      <option v-for="status in statuses" :key="status" :value="status">
        {{ status }}
      </option>
    </select>
  </div>
</template>

<script>
import { Inertia } from '@inertiajs/inertia'
import { usePage } from '@inertiajs/inertia-vue3'
import { ref, watch } from 'vue'

export default {
  setup() {
    const page = usePage()
    const loading = ref(false)
    const selectedStatus = ref(page.props.value.input.status)

    watch(selectedStatus, (status) => {
      handleStatusChange(status)
    })

    function handleStatusChange(status) {
      loading.value = true

      Inertia.get('/monitored-jobs', {
        ...page.props.value.input,
        status
      }, {
        preserveState: true,
        onFinish: () => loading.value = false
      })
    }

    return {
      selectedStatus,
      statuses: page.props.value.statuses
    }
  }
}
</script>