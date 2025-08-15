<template>
    <div class="p-3 bg-light rounded">
        <h2>Home Page</h2>
        <p>Welcome to the home page!</p>
        <button class="btn btn-primary" @click="increment">
            Clicked {{ count }} times
        </button>
    </div>
</template>

<script>
import { useCounterStore } from '../store/counter';

export default {
    name: 'Home',
    data() {
        return {};
    },
    computed: {
        count() {
            return this.counter.count;
        },
    },
    methods: {
        increment() {
            this.counter.increment();
        },
    },
    created() {
        this.counter = useCounterStore();
    },
};
</script>