<template>
  <div class="hello">
    <h1>{{ msg }}</h1>
    <p>yeah</p>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  data () {
    return {
      // note: changing this line won't causes changes
      // with hot-reload because the reloaded component
      // preserves its current state and we are modifying
      // its initial state.
      msg: 'Hello World Moep!'
    }
  }
}
</script>
