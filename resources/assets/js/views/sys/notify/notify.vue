<template>
  <Card>
    <Tabs value="baseInfo">
      <TabPane label="基础信息" name="baseInfo">
        <Col span="20">
          <p>您有{{project}}项目未审核</p>
        </Col>
        <Col span="4" >
          <Button
            @click="getUrl('projects')"
            type="primary"
            style="margin-left:8px"
          >查看
          </Button>
          <!-- <a class="right" href="/#/investment/projects?is_audit=0"></a> -->
        </Col>
        <Col span="20">
          <p>您有{{schedule}}项目进度未审核</p>
        </Col>
        <Col span="4">
          <Button
            @click="getUrl('projectSchedule')"
            type="primary"
            style="margin-left:8px"
          >查看
          </Button>
          <!-- <a class="right" href="/#/investment/projectSchedule?is_audit=0">查看</a> -->
        </Col>
      </TabPane>
    </Tabs>
  </Card>
</template>
<script>
  import './notify.css';
  import {getNoAudit} from 'api/project';

  export default {
    data() {
      return {
        project:0,
        schedule:0
      }
    },
    methods: {
      init() {
        this.getNoAudit();
      },
      getNoAudit(){
        getNoAudit().then(res => {
          this.project=res.result.projects;
          this.schedule=res.result.schedule;
        });
      },
      getUrl(name){
        this.$router.push({
            name: name,
            params: { is_audit: 0 }
          });
      }

    },
    mounted() {
      this.init();
    }
  }
</script>
