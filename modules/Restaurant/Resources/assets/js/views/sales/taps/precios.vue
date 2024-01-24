<template>
    <div class="row mt-5">
        <div class="col-xl-8 col-md-8 col-sm-12 offset-2" v-loading="loading">
            <el-table
                :data="tableData.filter(data => !search || data.producto.toLowerCase().includes(search.toLowerCase()))"
                style="width: 100%;font-size: 1.5em">
                <el-table-column
                    label="Codigo"
                    prop="internal_id">
                </el-table-column>

                <el-table-column
                    label="Descripción"
                    prop="description">
                </el-table-column>

                <el-table-column
                    label="Precio"
                    prop="price">
                </el-table-column>
                <el-table-column
                    label="Foto">

                    <template slot-scope="scope">
                        <el-image
                            style="width: 100px; height: 100px"
                            :src="scope.row.image_url_small"></el-image>
                    </template>
                </el-table-column>
                <el-table-column
                    align="right">
                    <template slot="header" slot-scope="scope">
                        <el-input
                            v-model="search"
                            size="mini"
                            placeholder="Buscar"/>
                    </template>
                    <template slot-scope="scope">
                        <el-button
                            size="mini"
                            type="primary"
                            icon="el-icon-edit"
                            @click="handleEdit(scope.$index, scope.row)">Editar Precio</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>
        <tenant-restaurant-dialog-precio
            :visible.sync="visible"
            :item="item"
            @getProducts="getProducts"
        ></tenant-restaurant-dialog-precio>
    </div>
</template>

<script>
export default {
    data() {
        return {
            tableData: [],
            search: '',
            visible:false,
            item: {}
        }
    },
    created() {
        this.getProducts()
    },
    methods: {
        handleEdit(index, row) {
            console.log(index, row);
            this.item=row
            this.visible = true
        },
        handleDelete(index, row) {
            console.log(index, row);
        },
        async getProducts(){
            try{
                this.loading = true;
                const { data } = await this.$http.get(`/restaurant/taps/items`);
                this.loading = false;
                console.log(data.data)
                this.tableData = data.data;

            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);

            }
        }

    },
}
</script>
