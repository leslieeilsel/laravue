<!--<template>-->
<!--  <baidu-map class="map" :center="{lng: 108.720027, lat: 34.298497}" :zoom="4">-->
<!--      <div v-for="item in items" :key="item.id">-->
<!--      <bm-marker :position="{lng: item.lng, lat: item.lat}" :dragging="true" @click="infoWindowOpen">-->
<!--  -->
<!--        <bm-info-window :position="{lng: item.lng, lat: item.lat}" title="Info Window Title" :show="infoWindow.show" @close="infoWindowClose" @open="infoWindowOpen">-->
<!--          <p v-text="infoWindow.contents"></p>-->
<!--          <button @click="clear">Clear</button>-->
<!--        </bm-info-window>-->
<!--&lt;!&ndash;          <bm-info-window :show="show" @close="infoWindowClose" @open="infoWindowOpen">我爱北京天安门</bm-info-window>&ndash;&gt;-->
<!--      </bm-marker>-->
<!--      </div>-->
<!--    <bm-navigation anchor="BMAP_ANCHOR_TOP_RIGHT"></bm-navigation>-->
<!--  </baidu-map>-->
<!--</template>-->

<!--<script>-->
<!--  import {getAllDepartment} from '../../../api/project'-->
<!--  export default {-->
<!--    data () {-->
<!--      return {-->
<!--        show: false,-->
<!--        items: [],-->
<!--        infoWindow: {-->
<!--          show: true,-->
<!--          contents: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'-->
<!--        }-->
<!--      }-->
<!--    },-->
<!--    methods: {-->
<!--      infoWindowClose (e) {-->
<!--        this.infoWindow.show = false-->
<!--      },-->
<!--      infoWindowOpen (e) {-->
<!--        this.infoWindow.show = true-->
<!--      },-->
<!--      clear () {-->
<!--        this.infoWindow.contents = ''-->
<!--      }-->
<!--      // addPoints () {-->
<!--      //   const points = [];-->
<!--      //   for (var i = 0; i < 1000; i++) {-->
<!--      //     const position = {lng: Math.random() * 40 + 85, lat: Math.random() * 30 + 21}-->
<!--      //     points.push(position)-->
<!--      //   }-->
<!--      //   this.points = points-->
<!--      // }-->
<!--    },-->
<!--    mounted() {-->
<!--      const items = [];-->
<!--      for (var i = 0; i < 10; i++) {-->
<!--        const position = {id: i, lng: Math.random() * 40 + 85, lat: Math.random() * 30 + 21}-->
<!--        items.push(position)-->
<!--      }-->
<!--      this.items = items-->
<!--    }-->
<!--  }-->
<!--</script>-->
<template>
  <div>
    <baidu-map class="map" :center="{lng: 108.720027, lat: 34.298497}" :zoom="15">
      <bm-point-collection :points="points" shape="BMAP_POINT_SHAPE_CIRCLE" color="red" size="BMAP_POINT_SIZE_BIGGER" @click="clickHandler"></bm-point-collection>
      <bm-navigation anchor="BMAP_ANCHOR_TOP_RIGHT"></bm-navigation>
    </baidu-map>
    <Modal :title="modalTitle" v-model="modalVisible" :mask-closable="false" :width="500">
      <Form ref="formAdd" :model="formAdd" :label-width="85" :rules="formValidate">
        <FormItem label="项目名称" prop="title">
          <Input readonly v-model="formAdd.title" placeholder=""/>
        </FormItem>
        <div v-if="addSon">
          <FormItem label="业主" prop="owner">
            <Input readonly v-model="formAdd.owner" placeholder=""/>
          </FormItem>
          <FormItem label="建设单位" prop="unit">
            <Input readonly v-model="formAdd.unit" placeholder=""/>
          </FormItem>
          <FormItem label="项目金额" prop="amount">
            <Input readonly v-model="formAdd.amount" placeholder="支持小数点后两位"/>
          </FormItem>
        </div>
        <FormItem label="计划时间">
          <Row>
            <Col span="11">
              <DatePicker readonly type="date" placeholder="开始时间" format="yyyy-MM-dd" v-model="formAdd.plan_start_at"></DatePicker>
            </Col>
            <Col span="2" style="text-align: center">-</Col>
            <Col span="11">
              <DatePicker readonly type="date" placeholder="结束时间" format="yyyy-MM-dd" v-model="formAdd.plan_end_at"></DatePicker>
            </Col>
          </Row>
        </FormItem>
        <FormItem label="实际时间">
          <Row>
            <Col span="11">
              <DatePicker readonly type="date" placeholder="开始时间" format="yyyy-MM-dd" v-model="formAdd.actual_start_at"></DatePicker>
            </Col>
            <Col span="2" style="text-align: center">-</Col>
            <Col span="11">
              <DatePicker readonly type="date" placeholder="结束时间" format="yyyy-MM-dd" v-model="formAdd.actual_end_at"></DatePicker>
            </Col>
          </Row>
        </FormItem>
      </Form>
      <div slot="footer">
        <Button type="primary" @click="detailInfo">查看详细信息</Button>
      </div>
    </Modal>
  </div>
</template>

<script>
  import {getAllDepartment} from '../../../api/project';
  
  export default {
    data () {
      return {
        points: [],
        show: false,
        modalVisible: false,
        addSon: true,
        formAdd: {},
        modalTitle: '详细信息',
        formValidate: {
          // 表单验证规则
          title: [{required: true, message: "名称不能为空", trigger: "blur"}]
        },
      }
    },
    methods: {
      init () {
        this.getAllProject();
      },
      clickHandler (e) {
        this.addSon = true;
        // alert(`单击点的坐标为：${e.point.lng}, ${e.point.lat}`);
        if (e.point.parent_id !== 0) {
          this.addSon = false;
        }
        this.modalVisible = true;
        this.formAdd = e.point;
        
      },
      infoWindowClose () {
        this.show = false
      },
      getAllProject () {
        getAllDepartment().then(res => {
          if (res.result) {
            this.points = res.result;
          }
        });
      },
      detailInfo() {
        this.modalVisible = false;
        this.$router.push({name: 'projectInfo'})
      }
    },
    mounted() {
      this.init();
    }
  }
</script>
<style>
  .map {
    width: 100%;
    height: 650px;
  }
</style>