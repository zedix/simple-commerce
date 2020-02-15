<template>
    <div>
        <section class="bg-grey-20 rounded w-full mt-2">
            <table class="bg-white data-table">
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Price</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="zone in items" :key="zone.id">
                        <td v-if="zone.state_id">{{ zone.country.name }}, {{ zone.state.name }}, {{ zone.start_of_zip_code }}</td>
                        <td v-else>{{ zone.country.name }}, {{ zone.start_of_zip_code }}</td>

                        <td>{{ zone.formatted_price }}</td>

                        <td class="flex justify-end">
                            <dropdown-list>
                                <dropdown-item text="Edit" @click="updateShippingZone(zone)"></dropdown-item>
                                <dropdown-item class="warning" text="Delete" :redirect="zone.deleteUrl"></dropdown-item>
                            </dropdown-list>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="flex items-center flex-row p-2">
                <button class="btn btn-primary" @click="createStackOpen = true">
                    Add Shipping Zone
                </button>
            </div>
        </section>

        <create-stack
                v-if="createStackOpen"
                title="Create Shipping Zone"
                :action="storeEndpoint"
                :blueprint="blueprint"
                :meta="meta"
                :values="values"
                @closed="createStackOpen = false"
                @saved="zoneSaved"
        ></create-stack>

        <update-stack
                v-if="editStackOpen"
                title="Update Shipping Zone"
                :action="editZone.updateUrl"
                :blueprint="blueprint"
                :meta="meta"
                :values="editZone"
                @closed="editStackOpen = false"
                @saved="zoneSaved"
        ></update-stack>
    </div>
</template>

<script>
    import axios from 'axios'
    import CreateStack from "../Stacks/CreateStack";
    import UpdateStack from "../Stacks/UpdateStack";

    export default {
        name: "ShippingZone",

        components: {
            CreateStack,
            UpdateStack
        },

        props: {
            indexEndpoint: String,
            storeEndpoint: String,
            initialBlueprint: Array,
            initialMeta: Array,
            initialValues: Array
        },

        data() {
            return {
                blueprint: JSON.parse(this.initialBlueprint),
                meta: JSON.parse(this.initialMeta),
                values: JSON.parse(this.initialValues),

                items: [],
                editZone: [],

                createStackOpen: false,
                editStackOpen: false
            }
        },

        methods: {
            getZones() {
                axios.get(this.indexEndpoint)
                    .then(response => {
                        this.items = response.data;
                    }).catch(error => {
                        this.$toast.error(error);
                    })
            },

            updateShippingZone(zone) {
                this.editZone = zone;
                this.editStackOpen = true;
            },

            zoneSaved() {
                this.createStackOpen = false;
                this.getZones();
            },

            zoneUpdated() {
                this.editStackOpen = false;
                this.getZones();
            },
        },

        mounted() {
            this.getZones();
        }
    }
</script>

<style scoped>

</style>