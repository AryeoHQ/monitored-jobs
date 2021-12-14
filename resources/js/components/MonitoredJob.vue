<template>
  <div class="mt-4">
    <h1>{{ monitoredJob.name }}</h1>
    <p>Status: {{ monitoredJob.current_status }}</p>

    <summary>
      <h2>Job Details</h2>

      <details>
        <pre>
<code>{{ monitoredJob.job_details }}</code>
        </pre>
      </details>
    </summary>

    <summary>
      <h2>Payload</h2>

      <details>
        <pre>
<code>{{ monitoredJob.payload }}</code>
        </pre>
      </details>
    </summary>

    <h2>Status History</h2>
    <div>
      <div v-for="status in monitoredJob.statuses" :key="status.id">
        {{ status.value }} {{ status.created_at }}
      </div>
    </div>

    <h2>Duration</h2>
    <div>
      {{ monitoredJob.duration }}
    </div>

    <h2>Tags</h2>
    <div>
      <div v-for="tag in monitoredJob.tags" :key="tag.id">
        {{ tag.value }}
      </div>
    </div>

    <h2>Events</h2>
    <div>
      <div v-for="event in monitoredJob.events" :key="event.id">
        {{ event.value }}
      </div>
    </div>

    <hr />
  </div>
</template>

<script>
export default {
  props: {
    monitoredJob: {
      type: Object,
      required: true
    }
  }
}
</script>