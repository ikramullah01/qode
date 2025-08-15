<template>
    <div class="p-3 bg-light rounded">
        <h2>About Page</h2>
        <p>This is the about page. You can display app info here.</p>

        <div class="mt-3">
            <p class="fw-bold">Current Counter Value (from Pinia): {{ count }}</p>
            <button class="btn btn-success me-2" @click="increment">Increment Counter</button>
            <button class="btn btn-danger" @click="reset">Reset Counter</button>
        </div>
    </div>
</template>

<script>
import { useCounterStore } from '../store/counter';

export default {
    name: 'About',
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
        reset() {
            this.counter.reset();
        },
    },
    created() {
        this.counter = useCounterStore();
    },
};
</script>