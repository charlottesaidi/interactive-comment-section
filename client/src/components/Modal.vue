<template>
    <div id="popup1" class="overlay">
        <div class="popup">
            <div class="content">
                <slot/>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'modal'
}
</script>