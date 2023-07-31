<template>
  <el-dialog
    title="注册协议"
    :visible.sync="dialogVisible"
    width="600px"
    :show-close="false"
    :destroy-on-close="false"
    :close-on-click-modal="false"
    :close-on-press-escape="false"
    @close="cancelFunc"
  >
    <div id="scroll-view" ref="scrollView" class="scroll-view" @scroll="handleScroll">
      本隐私声明将帮助你了解以下内容：
      一、我们如何收集和使用你的个人信息
      二、我们如何使用Cookies和同类技术
      三、我们如何“共享”“转让”“公开披露”你的个人信息
      四、我们如何保护和保存你的个人信息
      五、你如何管理个人信息
      六、未成年人的个人信息保护
      七、通知和修订
      八、如何联系我们
      本声明适用于有赞提供的所有产品和服务。如有赞及关联公司（范围详见定义部分）的产品或服务中使用了有赞提供的产品或服务（例如使用有赞帐号登录）但未设独立隐私权声明的，则本声明同样适用于该部分产品或服务。我们及关联公司就其向你提供的产品或服务单独设立有隐私权声明的（如《有赞支付隐私政策》、《有赞教育隐私声明》），则相应产品或服务适用相应隐私声明。
      需要特别说明的是，本隐私声明不适用于其他第三方通过前述网页或客户端或者小程序或公众号等向你提供的服务。例如你通过有赞使用第三方应用（如有赞应用市场）或者服务，或第三方服务和产品依托有赞产品向你提供服务时，你向第三方提供的信息不适用本声明。
      在使用有赞各项产品或服务前，请你务必仔细阅读和理解对应的隐私声明，特别是以粗体/粗体下划线标识的条款，你应重点阅读，确认你充分理解并同意后再开始使用。
      如对本声明内容有任何疑问、意见或建议，你可与我们联系。
      我们将担任“个人信息处理者”，亦即我们将决定“为何”和“如何”收集和使用你的个人信息”。
      为提供我们的产品及服务给你，我们需要和我们的“个人信息受托者”，即认可的商业伙伴或我们的关联合作伙伴共享你的个人信息。他们均负有采用适当安全措施以保护所持个人信息的义务，受严格的保密义务所规范，且“为何”和“如何”可以代替我们处理你的个人信息均受相关合同条款所约束。
      请注意部分认可的商业伙伴，如被授权商或社交媒体，也可以决定“为何”和“如何”使用你的个人信息。他们有各自的隐私和cookies政策，故请谨记他们使用你提供给他们的任何个人信息的方式应适用他们的规定，而不适用我们的规定。
      如你作为消费者，你授权给商家的个人信息，我们仅作为商家的个人信息受托者。
      为了解我们的角色，请注意我们“为何”和“如何”收集和使用你的个人信息，来决定处理你个人信息的目的和方式。我们的“个人信息受托者”则是由我们指示“为何”和“如何”可以代替我们处理你个人信息的机构。
      第一部分定义
      一、【有赞】有赞指研发并提供有赞产品和服务的杭州有赞科技有限公司（注册地址：浙江省杭州市西湖区西溪路698号6幢102室），及现在或未来设立的相关关联公司的单称或合称。
      二、【关联公司】关联公司指中国有赞有限公司（China Youzan Limited）最新上市公司年报披露的有赞服务提供者的关联公司，具体信息访问以下链接https://www.chinayouzan.com/notice?type=0。
      三、【有赞服务】有赞服务指由有赞开发和服务的有赞微商城、有赞美业、有赞教育、有赞连锁、有赞精选等全面帮助商家经营移动社交电商和全渠道新零售的SaaS软件产品及人才服务、面向开发者的“有赞云”PaaS云服务，面向品牌商的有赞推广、有赞分销、有赞客，面向消费者的购物软件有赞精选、有赞微小店等服务，以及将有赞部分功能/服务作为第三方服务集成在有赞关联公司（见定义条款）和/或其他第三方软件和/或智能硬件而提供的服务。
      四、【个人信息删除】个人信息删除指在实现日常业务功能所涉及的系统中去除个人信息的行为，使其保持不可被检索、访问的状态。
      五、【有赞支付】有赞支付即第三方支付公司，特指北京高汇通商业管理有限公司。
      六、除另有约定外，本声明所用定义与《用户协议》中的定义具有相同的涵义。
      第二部分 隐私声明
      一、我们如何收集和使用你的个人信息
      个人信息是指以电子或者其他方式记录的能够单独或者与其他信息结合识别特定自然人身份或者反映特定自然人活动情况的各种信息。个人敏感信息是指一旦泄露、非法提供或滥用可能危害人身和财产安全，极易导致个人名誉、身心健康受到损害或歧视性待遇的个人信息。本隐私声明中涉及的个人信息包括：基本信息（包括个人姓名、生日、性别、住址、个人电话号码、电子邮箱）；个人身份信息（包括身份证、军官证、护照、驾驶证等）；个人生物识别信息（包括声纹、面部识别特征等）；网络身份标识信息（包括个人信息主体帐号、IP地址、个人数字证书等）；个人财产信息（包括交易和消费记录、零钱余额、优惠券、权益卡、游戏或充值兑换卡等虚拟财产信息）；个人通讯信息（通讯录）；个人上网记录（包括网页浏览记录、软件使用记录、点击记录）；个人常用设备信息（包括硬件型号、设备MAC地址、操作系统类型、设备设置、运行中进程信息、软件安装列表、已经安装包名列表、硬件序列号、唯一设备识别码（如IMEI/OAID/android ID/IDFA/OPENUDID/GUID、SIM卡序列号、SIM卡IMSI信息等在内的描述个人常用设备基本情况的信息））；个人位置信息（包粗略定位信息、精准定位信息等）；其中，字体加粗部分为涉及的个人敏感信息。
      我们会通过如下三种渠道获取你的个人信息：
      （一）你直接提供给我们及我们收集的个人信息
      请你注意，如果你提供的是他人个人信息，请你确保已取得相关主体的授权。
    </div>

    <div slot="footer" class="dialog-footer">
      <el-button size="small" type="primary" :class="is_show === '0' ? 'verif-button-close' : ''" @click="cancelFunc">确认阅读</el-button>
    </div>
  </el-dialog>
</template>

<script>
export default {
  props: {
    // eslint-disable-next-line vue/prop-name-casing
    agreementOpen: Boolean
  },
  data() {
    return {
      /* 是否加载完成 */
      loading: true,
      /* 是否显示 */
      dialogVisible: false,
      is_show: '0'
    }
  },
  watch: {
    agreementOpen: function(n, o) {
      if (n !== o) {
        this.dialogVisible = n
      }
    }
  },
  created() {},
  methods: {
    handleScroll(e) {
      const { scrollTop, clientHeight, scrollHeight } = e.target
      // console.log(scrollTop, clientHeight, scrollHeight)
      if (scrollTop + clientHeight === scrollHeight) {
        this.is_show = '1'
      }
    },
    /** 关闭弹窗 **/
    cancelFunc(e) {
      if (this.is_show === '1') {
        this.$emit('closeAgreement', {
          type: 'error'
        })
      }
    }
  }
}
</script>

<style>
	.scroll-view {
		max-height:450px;
		overflow-y:scroll;
		white-space: pre-wrap;
		padding:20px;
		font-size: 14px;
		text-align: left;
		font-family: SourceHanSansSC-regular;
	}
	.verif-button-close {
		background-color: rgba(242, 242, 242, 1);
		color: rgba(210, 210, 210, 1);
		border:0;
	}
	.verif-button-close:active {
		background-color: rgba(242, 242, 242, 1);
		color: rgba(210, 210, 210, 1);
		border:0;
  }
  .verif-button-close:focus {
    background-color: rgba(242, 242, 242, 1);
    color: rgba(210, 210, 210, 1);
    border:0;
  }
  .verif-button-close:hover {
    background-color: rgba(242, 242, 242, 1);
    color: rgba(210, 210, 210, 1);
    border:0;
  }
</style>
