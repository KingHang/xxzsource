/**
 * 省市区三级联动选择
 */
const REGIONS = {
  '1': {
    'id': 1, 'pid': 0, 'name': '\u5317\u4eac\u5e02', 'level': 1, 'city': {
      '2': {
        'id': 2,
        'pid': 1,
        'name': '\u5317\u4eac\u5e02',
        'level': 2,
        'region': {
          '3': { 'id': 3, 'pid': 2, 'name': '\u4e1c\u57ce\u533a', 'level': 3 },
          '4': { 'id': 4, 'pid': 2, 'name': '\u897f\u57ce\u533a', 'level': 3 },
          '5': { 'id': 5, 'pid': 2, 'name': '\u671d\u9633\u533a', 'level': 3 },
          '6': { 'id': 6, 'pid': 2, 'name': '\u4e30\u53f0\u533a', 'level': 3 },
          '7': { 'id': 7, 'pid': 2, 'name': '\u77f3\u666f\u5c71\u533a', 'level': 3 },
          '8': { 'id': 8, 'pid': 2, 'name': '\u6d77\u6dc0\u533a', 'level': 3 },
          '9': { 'id': 9, 'pid': 2, 'name': '\u95e8\u5934\u6c9f\u533a', 'level': 3 },
          '10': { 'id': 10, 'pid': 2, 'name': '\u623f\u5c71\u533a', 'level': 3 },
          '11': { 'id': 11, 'pid': 2, 'name': '\u901a\u5dde\u533a', 'level': 3 },
          '12': { 'id': 12, 'pid': 2, 'name': '\u987a\u4e49\u533a', 'level': 3 },
          '13': { 'id': 13, 'pid': 2, 'name': '\u660c\u5e73\u533a', 'level': 3 },
          '14': { 'id': 14, 'pid': 2, 'name': '\u5927\u5174\u533a', 'level': 3 },
          '15': { 'id': 15, 'pid': 2, 'name': '\u6000\u67d4\u533a', 'level': 3 },
          '16': { 'id': 16, 'pid': 2, 'name': '\u5e73\u8c37\u533a', 'level': 3 },
          '17': { 'id': 17, 'pid': 2, 'name': '\u5bc6\u4e91\u53bf', 'level': 3 },
          '18': { 'id': 18, 'pid': 2, 'name': '\u5ef6\u5e86\u53bf', 'level': 3 }
        }
      }
    }
  },
  '19': {
    'id': 19, 'pid': 0, 'name': '\u5929\u6d25\u5e02', 'level': 1, 'city': {
      '20': {
        'id': 20,
        'pid': 19,
        'name': '\u5929\u6d25\u5e02',
        'level': 2,
        'region': {
          '21': { 'id': 21, 'pid': 20, 'name': '\u548c\u5e73\u533a', 'level': 3 },
          '22': { 'id': 22, 'pid': 20, 'name': '\u6cb3\u4e1c\u533a', 'level': 3 },
          '23': { 'id': 23, 'pid': 20, 'name': '\u6cb3\u897f\u533a', 'level': 3 },
          '24': { 'id': 24, 'pid': 20, 'name': '\u5357\u5f00\u533a', 'level': 3 },
          '25': { 'id': 25, 'pid': 20, 'name': '\u6cb3\u5317\u533a', 'level': 3 },
          '26': { 'id': 26, 'pid': 20, 'name': '\u7ea2\u6865\u533a', 'level': 3 },
          '27': { 'id': 27, 'pid': 20, 'name': '\u4e1c\u4e3d\u533a', 'level': 3 },
          '28': { 'id': 28, 'pid': 20, 'name': '\u897f\u9752\u533a', 'level': 3 },
          '29': { 'id': 29, 'pid': 20, 'name': '\u6d25\u5357\u533a', 'level': 3 },
          '30': { 'id': 30, 'pid': 20, 'name': '\u5317\u8fb0\u533a', 'level': 3 },
          '31': { 'id': 31, 'pid': 20, 'name': '\u6b66\u6e05\u533a', 'level': 3 },
          '32': { 'id': 32, 'pid': 20, 'name': '\u5b9d\u577b\u533a', 'level': 3 },
          '33': { 'id': 33, 'pid': 20, 'name': '\u6ee8\u6d77\u65b0\u533a', 'level': 3 },
          '34': { 'id': 34, 'pid': 20, 'name': '\u5b81\u6cb3\u53bf', 'level': 3 },
          '35': { 'id': 35, 'pid': 20, 'name': '\u9759\u6d77\u53bf', 'level': 3 },
          '36': { 'id': 36, 'pid': 20, 'name': '\u84df\u53bf', 'level': 3 }
        }
      }
    }
  },
  '37': {
    'id': 37, 'pid': 0, 'name': '\u6cb3\u5317\u7701', 'level': 1, 'city': {
      '38': {
        'id': 38, 'pid': 37, 'name': '\u77f3\u5bb6\u5e84\u5e02', 'level': 2, 'region': {
          '39': { 'id': 39, 'pid': 38, 'name': '\u957f\u5b89\u533a', 'level': 3 },
          '40': { 'id': 40, 'pid': 38, 'name': '\u6865\u897f\u533a', 'level': 3 },
          '41': { 'id': 41, 'pid': 38, 'name': '\u65b0\u534e\u533a', 'level': 3 },
          '42': { 'id': 42, 'pid': 38, 'name': '\u4e95\u9649\u77ff\u533a', 'level': 3 },
          '43': { 'id': 43, 'pid': 38, 'name': '\u88d5\u534e\u533a', 'level': 3 },
          '44': { 'id': 44, 'pid': 38, 'name': '\u85c1\u57ce\u533a', 'level': 3 },
          '45': { 'id': 45, 'pid': 38, 'name': '\u9e7f\u6cc9\u533a', 'level': 3 },
          '46': { 'id': 46, 'pid': 38, 'name': '\u683e\u57ce\u533a', 'level': 3 },
          '47': { 'id': 47, 'pid': 38, 'name': '\u4e95\u9649\u53bf', 'level': 3 },
          '48': { 'id': 48, 'pid': 38, 'name': '\u6b63\u5b9a\u53bf', 'level': 3 },
          '49': { 'id': 49, 'pid': 38, 'name': '\u884c\u5510\u53bf', 'level': 3 },
          '50': { 'id': 50, 'pid': 38, 'name': '\u7075\u5bff\u53bf', 'level': 3 },
          '51': { 'id': 51, 'pid': 38, 'name': '\u9ad8\u9091\u53bf', 'level': 3 },
          '52': { 'id': 52, 'pid': 38, 'name': '\u6df1\u6cfd\u53bf', 'level': 3 },
          '53': { 'id': 53, 'pid': 38, 'name': '\u8d5e\u7687\u53bf', 'level': 3 },
          '54': { 'id': 54, 'pid': 38, 'name': '\u65e0\u6781\u53bf', 'level': 3 },
          '55': { 'id': 55, 'pid': 38, 'name': '\u5e73\u5c71\u53bf', 'level': 3 },
          '56': { 'id': 56, 'pid': 38, 'name': '\u5143\u6c0f\u53bf', 'level': 3 },
          '57': { 'id': 57, 'pid': 38, 'name': '\u8d75\u53bf', 'level': 3 },
          '58': { 'id': 58, 'pid': 38, 'name': '\u8f9b\u96c6\u5e02', 'level': 3 },
          '59': { 'id': 59, 'pid': 38, 'name': '\u664b\u5dde\u5e02', 'level': 3 },
          '60': { 'id': 60, 'pid': 38, 'name': '\u65b0\u4e50\u5e02', 'level': 3 }
        }
      },
      '61': {
        'id': 61,
        'pid': 37,
        'name': '\u5510\u5c71\u5e02',
        'level': 2,
        'region': {
          '62': { 'id': 62, 'pid': 61, 'name': '\u8def\u5357\u533a', 'level': 3 },
          '63': { 'id': 63, 'pid': 61, 'name': '\u8def\u5317\u533a', 'level': 3 },
          '64': { 'id': 64, 'pid': 61, 'name': '\u53e4\u51b6\u533a', 'level': 3 },
          '65': { 'id': 65, 'pid': 61, 'name': '\u5f00\u5e73\u533a', 'level': 3 },
          '66': { 'id': 66, 'pid': 61, 'name': '\u4e30\u5357\u533a', 'level': 3 },
          '67': { 'id': 67, 'pid': 61, 'name': '\u4e30\u6da6\u533a', 'level': 3 },
          '68': { 'id': 68, 'pid': 61, 'name': '\u66f9\u5983\u7538\u533a', 'level': 3 },
          '69': { 'id': 69, 'pid': 61, 'name': '\u6ee6\u53bf', 'level': 3 },
          '70': { 'id': 70, 'pid': 61, 'name': '\u6ee6\u5357\u53bf', 'level': 3 },
          '71': { 'id': 71, 'pid': 61, 'name': '\u4e50\u4ead\u53bf', 'level': 3 },
          '72': { 'id': 72, 'pid': 61, 'name': '\u8fc1\u897f\u53bf', 'level': 3 },
          '73': { 'id': 73, 'pid': 61, 'name': '\u7389\u7530\u53bf', 'level': 3 },
          '74': { 'id': 74, 'pid': 61, 'name': '\u9075\u5316\u5e02', 'level': 3 },
          '75': { 'id': 75, 'pid': 61, 'name': '\u8fc1\u5b89\u5e02', 'level': 3 }
        }
      },
      '76': {
        'id': 76,
        'pid': 37,
        'name': '\u79e6\u7687\u5c9b\u5e02',
        'level': 2,
        'region': {
          '77': { 'id': 77, 'pid': 76, 'name': '\u6d77\u6e2f\u533a', 'level': 3 },
          '78': { 'id': 78, 'pid': 76, 'name': '\u5c71\u6d77\u5173\u533a', 'level': 3 },
          '79': { 'id': 79, 'pid': 76, 'name': '\u5317\u6234\u6cb3\u533a', 'level': 3 },
          '80': { 'id': 80, 'pid': 76, 'name': '\u9752\u9f99\u6ee1\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '81': { 'id': 81, 'pid': 76, 'name': '\u660c\u9ece\u53bf', 'level': 3 },
          '82': { 'id': 82, 'pid': 76, 'name': '\u629a\u5b81\u53bf', 'level': 3 },
          '83': { 'id': 83, 'pid': 76, 'name': '\u5362\u9f99\u53bf', 'level': 3 }
        }
      },
      '84': {
        'id': 84, 'pid': 37, 'name': '\u90af\u90f8\u5e02', 'level': 2, 'region': {
          '85': { 'id': 85, 'pid': 84, 'name': '\u90af\u5c71\u533a', 'level': 3 },
          '86': { 'id': 86, 'pid': 84, 'name': '\u4e1b\u53f0\u533a', 'level': 3 },
          '87': { 'id': 87, 'pid': 84, 'name': '\u590d\u5174\u533a', 'level': 3 },
          '88': { 'id': 88, 'pid': 84, 'name': '\u5cf0\u5cf0\u77ff\u533a', 'level': 3 },
          '89': { 'id': 89, 'pid': 84, 'name': '\u90af\u90f8\u53bf', 'level': 3 },
          '90': { 'id': 90, 'pid': 84, 'name': '\u4e34\u6f33\u53bf', 'level': 3 },
          '91': { 'id': 91, 'pid': 84, 'name': '\u6210\u5b89\u53bf', 'level': 3 },
          '92': { 'id': 92, 'pid': 84, 'name': '\u5927\u540d\u53bf', 'level': 3 },
          '93': { 'id': 93, 'pid': 84, 'name': '\u6d89\u53bf', 'level': 3 },
          '94': { 'id': 94, 'pid': 84, 'name': '\u78c1\u53bf', 'level': 3 },
          '95': { 'id': 95, 'pid': 84, 'name': '\u80a5\u4e61\u53bf', 'level': 3 },
          '96': { 'id': 96, 'pid': 84, 'name': '\u6c38\u5e74\u53bf', 'level': 3 },
          '97': { 'id': 97, 'pid': 84, 'name': '\u90b1\u53bf', 'level': 3 },
          '98': { 'id': 98, 'pid': 84, 'name': '\u9e21\u6cfd\u53bf', 'level': 3 },
          '99': { 'id': 99, 'pid': 84, 'name': '\u5e7f\u5e73\u53bf', 'level': 3 },
          '100': { 'id': 100, 'pid': 84, 'name': '\u9986\u9676\u53bf', 'level': 3 },
          '101': { 'id': 101, 'pid': 84, 'name': '\u9b4f\u53bf', 'level': 3 },
          '102': { 'id': 102, 'pid': 84, 'name': '\u66f2\u5468\u53bf', 'level': 3 },
          '103': { 'id': 103, 'pid': 84, 'name': '\u6b66\u5b89\u5e02', 'level': 3 }
        }
      },
      '104': {
        'id': 104, 'pid': 37, 'name': '\u90a2\u53f0\u5e02', 'level': 2, 'region': {
          '105': { 'id': 105, 'pid': 104, 'name': '\u6865\u4e1c\u533a', 'level': 3 },
          '106': { 'id': 106, 'pid': 104, 'name': '\u6865\u897f\u533a', 'level': 3 },
          '107': { 'id': 107, 'pid': 104, 'name': '\u90a2\u53f0\u53bf', 'level': 3 },
          '108': { 'id': 108, 'pid': 104, 'name': '\u4e34\u57ce\u53bf', 'level': 3 },
          '109': { 'id': 109, 'pid': 104, 'name': '\u5185\u4e18\u53bf', 'level': 3 },
          '110': { 'id': 110, 'pid': 104, 'name': '\u67cf\u4e61\u53bf', 'level': 3 },
          '111': { 'id': 111, 'pid': 104, 'name': '\u9686\u5c27\u53bf', 'level': 3 },
          '112': { 'id': 112, 'pid': 104, 'name': '\u4efb\u53bf', 'level': 3 },
          '113': { 'id': 113, 'pid': 104, 'name': '\u5357\u548c\u53bf', 'level': 3 },
          '114': { 'id': 114, 'pid': 104, 'name': '\u5b81\u664b\u53bf', 'level': 3 },
          '115': { 'id': 115, 'pid': 104, 'name': '\u5de8\u9e7f\u53bf', 'level': 3 },
          '116': { 'id': 116, 'pid': 104, 'name': '\u65b0\u6cb3\u53bf', 'level': 3 },
          '117': { 'id': 117, 'pid': 104, 'name': '\u5e7f\u5b97\u53bf', 'level': 3 },
          '118': { 'id': 118, 'pid': 104, 'name': '\u5e73\u4e61\u53bf', 'level': 3 },
          '119': { 'id': 119, 'pid': 104, 'name': '\u5a01\u53bf', 'level': 3 },
          '120': { 'id': 120, 'pid': 104, 'name': '\u6e05\u6cb3\u53bf', 'level': 3 },
          '121': { 'id': 121, 'pid': 104, 'name': '\u4e34\u897f\u53bf', 'level': 3 },
          '122': { 'id': 122, 'pid': 104, 'name': '\u5357\u5bab\u5e02', 'level': 3 },
          '123': { 'id': 123, 'pid': 104, 'name': '\u6c99\u6cb3\u5e02', 'level': 3 }
        }
      },
      '124': {
        'id': 124, 'pid': 37, 'name': '\u4fdd\u5b9a\u5e02', 'level': 2, 'region': {
          '125': { 'id': 125, 'pid': 124, 'name': '\u65b0\u5e02\u533a', 'level': 3 },
          '126': { 'id': 126, 'pid': 124, 'name': '\u5317\u5e02\u533a', 'level': 3 },
          '127': { 'id': 127, 'pid': 124, 'name': '\u5357\u5e02\u533a', 'level': 3 },
          '128': { 'id': 128, 'pid': 124, 'name': '\u6ee1\u57ce\u53bf', 'level': 3 },
          '129': { 'id': 129, 'pid': 124, 'name': '\u6e05\u82d1\u53bf', 'level': 3 },
          '130': { 'id': 130, 'pid': 124, 'name': '\u6d9e\u6c34\u53bf', 'level': 3 },
          '131': { 'id': 131, 'pid': 124, 'name': '\u961c\u5e73\u53bf', 'level': 3 },
          '132': { 'id': 132, 'pid': 124, 'name': '\u5f90\u6c34\u53bf', 'level': 3 },
          '133': { 'id': 133, 'pid': 124, 'name': '\u5b9a\u5174\u53bf', 'level': 3 },
          '134': { 'id': 134, 'pid': 124, 'name': '\u5510\u53bf', 'level': 3 },
          '135': { 'id': 135, 'pid': 124, 'name': '\u9ad8\u9633\u53bf', 'level': 3 },
          '136': { 'id': 136, 'pid': 124, 'name': '\u5bb9\u57ce\u53bf', 'level': 3 },
          '137': { 'id': 137, 'pid': 124, 'name': '\u6d9e\u6e90\u53bf', 'level': 3 },
          '138': { 'id': 138, 'pid': 124, 'name': '\u671b\u90fd\u53bf', 'level': 3 },
          '139': { 'id': 139, 'pid': 124, 'name': '\u5b89\u65b0\u53bf', 'level': 3 },
          '140': { 'id': 140, 'pid': 124, 'name': '\u6613\u53bf', 'level': 3 },
          '141': { 'id': 141, 'pid': 124, 'name': '\u66f2\u9633\u53bf', 'level': 3 },
          '142': { 'id': 142, 'pid': 124, 'name': '\u8821\u53bf', 'level': 3 },
          '143': { 'id': 143, 'pid': 124, 'name': '\u987a\u5e73\u53bf', 'level': 3 },
          '144': { 'id': 144, 'pid': 124, 'name': '\u535a\u91ce\u53bf', 'level': 3 },
          '145': { 'id': 145, 'pid': 124, 'name': '\u96c4\u53bf', 'level': 3 },
          '146': { 'id': 146, 'pid': 124, 'name': '\u6dbf\u5dde\u5e02', 'level': 3 },
          '147': { 'id': 147, 'pid': 124, 'name': '\u5b9a\u5dde\u5e02', 'level': 3 },
          '148': { 'id': 148, 'pid': 124, 'name': '\u5b89\u56fd\u5e02', 'level': 3 },
          '149': { 'id': 149, 'pid': 124, 'name': '\u9ad8\u7891\u5e97\u5e02', 'level': 3 }
        }
      },
      '150': {
        'id': 150, 'pid': 37, 'name': '\u5f20\u5bb6\u53e3\u5e02', 'level': 2, 'region': {
          '151': { 'id': 151, 'pid': 150, 'name': '\u6865\u4e1c\u533a', 'level': 3 },
          '152': { 'id': 152, 'pid': 150, 'name': '\u6865\u897f\u533a', 'level': 3 },
          '153': { 'id': 153, 'pid': 150, 'name': '\u5ba3\u5316\u533a', 'level': 3 },
          '154': { 'id': 154, 'pid': 150, 'name': '\u4e0b\u82b1\u56ed\u533a', 'level': 3 },
          '155': { 'id': 155, 'pid': 150, 'name': '\u5ba3\u5316\u53bf', 'level': 3 },
          '156': { 'id': 156, 'pid': 150, 'name': '\u5f20\u5317\u53bf', 'level': 3 },
          '157': { 'id': 157, 'pid': 150, 'name': '\u5eb7\u4fdd\u53bf', 'level': 3 },
          '158': { 'id': 158, 'pid': 150, 'name': '\u6cbd\u6e90\u53bf', 'level': 3 },
          '159': { 'id': 159, 'pid': 150, 'name': '\u5c1a\u4e49\u53bf', 'level': 3 },
          '160': { 'id': 160, 'pid': 150, 'name': '\u851a\u53bf', 'level': 3 },
          '161': { 'id': 161, 'pid': 150, 'name': '\u9633\u539f\u53bf', 'level': 3 },
          '162': { 'id': 162, 'pid': 150, 'name': '\u6000\u5b89\u53bf', 'level': 3 },
          '163': { 'id': 163, 'pid': 150, 'name': '\u4e07\u5168\u53bf', 'level': 3 },
          '164': { 'id': 164, 'pid': 150, 'name': '\u6000\u6765\u53bf', 'level': 3 },
          '165': { 'id': 165, 'pid': 150, 'name': '\u6dbf\u9e7f\u53bf', 'level': 3 },
          '166': { 'id': 166, 'pid': 150, 'name': '\u8d64\u57ce\u53bf', 'level': 3 },
          '167': { 'id': 167, 'pid': 150, 'name': '\u5d07\u793c\u53bf', 'level': 3 }
        }
      },
      '168': {
        'id': 168,
        'pid': 37,
        'name': '\u627f\u5fb7\u5e02',
        'level': 2,
        'region': {
          '169': { 'id': 169, 'pid': 168, 'name': '\u53cc\u6865\u533a', 'level': 3 },
          '170': { 'id': 170, 'pid': 168, 'name': '\u53cc\u6ee6\u533a', 'level': 3 },
          '171': { 'id': 171, 'pid': 168, 'name': '\u9e70\u624b\u8425\u5b50\u77ff\u533a', 'level': 3 },
          '172': { 'id': 172, 'pid': 168, 'name': '\u627f\u5fb7\u53bf', 'level': 3 },
          '173': { 'id': 173, 'pid': 168, 'name': '\u5174\u9686\u53bf', 'level': 3 },
          '174': { 'id': 174, 'pid': 168, 'name': '\u5e73\u6cc9\u53bf', 'level': 3 },
          '175': { 'id': 175, 'pid': 168, 'name': '\u6ee6\u5e73\u53bf', 'level': 3 },
          '176': { 'id': 176, 'pid': 168, 'name': '\u9686\u5316\u53bf', 'level': 3 },
          '177': { 'id': 177, 'pid': 168, 'name': '\u4e30\u5b81\u6ee1\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '178': { 'id': 178, 'pid': 168, 'name': '\u5bbd\u57ce\u6ee1\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '179': {
            'id': 179,
            'pid': 168,
            'name': '\u56f4\u573a\u6ee1\u65cf\u8499\u53e4\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          }
        }
      },
      '180': {
        'id': 180, 'pid': 37, 'name': '\u6ca7\u5dde\u5e02', 'level': 2, 'region': {
          '181': { 'id': 181, 'pid': 180, 'name': '\u65b0\u534e\u533a', 'level': 3 },
          '182': { 'id': 182, 'pid': 180, 'name': '\u8fd0\u6cb3\u533a', 'level': 3 },
          '183': { 'id': 183, 'pid': 180, 'name': '\u6ca7\u53bf', 'level': 3 },
          '184': { 'id': 184, 'pid': 180, 'name': '\u9752\u53bf', 'level': 3 },
          '185': { 'id': 185, 'pid': 180, 'name': '\u4e1c\u5149\u53bf', 'level': 3 },
          '186': { 'id': 186, 'pid': 180, 'name': '\u6d77\u5174\u53bf', 'level': 3 },
          '187': { 'id': 187, 'pid': 180, 'name': '\u76d0\u5c71\u53bf', 'level': 3 },
          '188': { 'id': 188, 'pid': 180, 'name': '\u8083\u5b81\u53bf', 'level': 3 },
          '189': { 'id': 189, 'pid': 180, 'name': '\u5357\u76ae\u53bf', 'level': 3 },
          '190': { 'id': 190, 'pid': 180, 'name': '\u5434\u6865\u53bf', 'level': 3 },
          '191': { 'id': 191, 'pid': 180, 'name': '\u732e\u53bf', 'level': 3 },
          '192': { 'id': 192, 'pid': 180, 'name': '\u5b5f\u6751\u56de\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '193': { 'id': 193, 'pid': 180, 'name': '\u6cca\u5934\u5e02', 'level': 3 },
          '194': { 'id': 194, 'pid': 180, 'name': '\u4efb\u4e18\u5e02', 'level': 3 },
          '195': { 'id': 195, 'pid': 180, 'name': '\u9ec4\u9a85\u5e02', 'level': 3 },
          '196': { 'id': 196, 'pid': 180, 'name': '\u6cb3\u95f4\u5e02', 'level': 3 }
        }
      },
      '197': {
        'id': 197,
        'pid': 37,
        'name': '\u5eca\u574a\u5e02',
        'level': 2,
        'region': {
          '198': { 'id': 198, 'pid': 197, 'name': '\u5b89\u6b21\u533a', 'level': 3 },
          '199': { 'id': 199, 'pid': 197, 'name': '\u5e7f\u9633\u533a', 'level': 3 },
          '200': { 'id': 200, 'pid': 197, 'name': '\u56fa\u5b89\u53bf', 'level': 3 },
          '201': { 'id': 201, 'pid': 197, 'name': '\u6c38\u6e05\u53bf', 'level': 3 },
          '202': { 'id': 202, 'pid': 197, 'name': '\u9999\u6cb3\u53bf', 'level': 3 },
          '203': { 'id': 203, 'pid': 197, 'name': '\u5927\u57ce\u53bf', 'level': 3 },
          '204': { 'id': 204, 'pid': 197, 'name': '\u6587\u5b89\u53bf', 'level': 3 },
          '205': { 'id': 205, 'pid': 197, 'name': '\u5927\u5382\u56de\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '206': { 'id': 206, 'pid': 197, 'name': '\u9738\u5dde\u5e02', 'level': 3 },
          '207': { 'id': 207, 'pid': 197, 'name': '\u4e09\u6cb3\u5e02', 'level': 3 }
        }
      },
      '208': {
        'id': 208,
        'pid': 37,
        'name': '\u8861\u6c34\u5e02',
        'level': 2,
        'region': {
          '209': { 'id': 209, 'pid': 208, 'name': '\u6843\u57ce\u533a', 'level': 3 },
          '210': { 'id': 210, 'pid': 208, 'name': '\u67a3\u5f3a\u53bf', 'level': 3 },
          '211': { 'id': 211, 'pid': 208, 'name': '\u6b66\u9091\u53bf', 'level': 3 },
          '212': { 'id': 212, 'pid': 208, 'name': '\u6b66\u5f3a\u53bf', 'level': 3 },
          '213': { 'id': 213, 'pid': 208, 'name': '\u9976\u9633\u53bf', 'level': 3 },
          '214': { 'id': 214, 'pid': 208, 'name': '\u5b89\u5e73\u53bf', 'level': 3 },
          '215': { 'id': 215, 'pid': 208, 'name': '\u6545\u57ce\u53bf', 'level': 3 },
          '216': { 'id': 216, 'pid': 208, 'name': '\u666f\u53bf', 'level': 3 },
          '217': { 'id': 217, 'pid': 208, 'name': '\u961c\u57ce\u53bf', 'level': 3 },
          '218': { 'id': 218, 'pid': 208, 'name': '\u5180\u5dde\u5e02', 'level': 3 },
          '219': { 'id': 219, 'pid': 208, 'name': '\u6df1\u5dde\u5e02', 'level': 3 }
        }
      }
    }
  },
  '220': {
    'id': 220, 'pid': 0, 'name': '\u5c71\u897f\u7701', 'level': 1, 'city': {
      '221': {
        'id': 221,
        'pid': 220,
        'name': '\u592a\u539f\u5e02',
        'level': 2,
        'region': {
          '222': { 'id': 222, 'pid': 221, 'name': '\u5c0f\u5e97\u533a', 'level': 3 },
          '223': { 'id': 223, 'pid': 221, 'name': '\u8fce\u6cfd\u533a', 'level': 3 },
          '224': { 'id': 224, 'pid': 221, 'name': '\u674f\u82b1\u5cad\u533a', 'level': 3 },
          '225': { 'id': 225, 'pid': 221, 'name': '\u5c16\u8349\u576a\u533a', 'level': 3 },
          '226': { 'id': 226, 'pid': 221, 'name': '\u4e07\u67cf\u6797\u533a', 'level': 3 },
          '227': { 'id': 227, 'pid': 221, 'name': '\u664b\u6e90\u533a', 'level': 3 },
          '228': { 'id': 228, 'pid': 221, 'name': '\u6e05\u5f90\u53bf', 'level': 3 },
          '229': { 'id': 229, 'pid': 221, 'name': '\u9633\u66f2\u53bf', 'level': 3 },
          '230': { 'id': 230, 'pid': 221, 'name': '\u5a04\u70e6\u53bf', 'level': 3 },
          '231': { 'id': 231, 'pid': 221, 'name': '\u53e4\u4ea4\u5e02', 'level': 3 }
        }
      },
      '232': {
        'id': 232,
        'pid': 220,
        'name': '\u5927\u540c\u5e02',
        'level': 2,
        'region': {
          '233': { 'id': 233, 'pid': 232, 'name': '\u57ce\u533a', 'level': 3 },
          '234': { 'id': 234, 'pid': 232, 'name': '\u77ff\u533a', 'level': 3 },
          '235': { 'id': 235, 'pid': 232, 'name': '\u5357\u90ca\u533a', 'level': 3 },
          '236': { 'id': 236, 'pid': 232, 'name': '\u65b0\u8363\u533a', 'level': 3 },
          '237': { 'id': 237, 'pid': 232, 'name': '\u9633\u9ad8\u53bf', 'level': 3 },
          '238': { 'id': 238, 'pid': 232, 'name': '\u5929\u9547\u53bf', 'level': 3 },
          '239': { 'id': 239, 'pid': 232, 'name': '\u5e7f\u7075\u53bf', 'level': 3 },
          '240': { 'id': 240, 'pid': 232, 'name': '\u7075\u4e18\u53bf', 'level': 3 },
          '241': { 'id': 241, 'pid': 232, 'name': '\u6d51\u6e90\u53bf', 'level': 3 },
          '242': { 'id': 242, 'pid': 232, 'name': '\u5de6\u4e91\u53bf', 'level': 3 },
          '243': { 'id': 243, 'pid': 232, 'name': '\u5927\u540c\u53bf', 'level': 3 }
        }
      },
      '244': {
        'id': 244,
        'pid': 220,
        'name': '\u9633\u6cc9\u5e02',
        'level': 2,
        'region': {
          '245': { 'id': 245, 'pid': 244, 'name': '\u57ce\u533a', 'level': 3 },
          '246': { 'id': 246, 'pid': 244, 'name': '\u77ff\u533a', 'level': 3 },
          '247': { 'id': 247, 'pid': 244, 'name': '\u90ca\u533a', 'level': 3 },
          '248': { 'id': 248, 'pid': 244, 'name': '\u5e73\u5b9a\u53bf', 'level': 3 },
          '249': { 'id': 249, 'pid': 244, 'name': '\u76c2\u53bf', 'level': 3 }
        }
      },
      '250': {
        'id': 250,
        'pid': 220,
        'name': '\u957f\u6cbb\u5e02',
        'level': 2,
        'region': {
          '251': { 'id': 251, 'pid': 250, 'name': '\u57ce\u533a', 'level': 3 },
          '252': { 'id': 252, 'pid': 250, 'name': '\u90ca\u533a', 'level': 3 },
          '253': { 'id': 253, 'pid': 250, 'name': '\u957f\u6cbb\u53bf', 'level': 3 },
          '254': { 'id': 254, 'pid': 250, 'name': '\u8944\u57a3\u53bf', 'level': 3 },
          '255': { 'id': 255, 'pid': 250, 'name': '\u5c6f\u7559\u53bf', 'level': 3 },
          '256': { 'id': 256, 'pid': 250, 'name': '\u5e73\u987a\u53bf', 'level': 3 },
          '257': { 'id': 257, 'pid': 250, 'name': '\u9ece\u57ce\u53bf', 'level': 3 },
          '258': { 'id': 258, 'pid': 250, 'name': '\u58f6\u5173\u53bf', 'level': 3 },
          '259': { 'id': 259, 'pid': 250, 'name': '\u957f\u5b50\u53bf', 'level': 3 },
          '260': { 'id': 260, 'pid': 250, 'name': '\u6b66\u4e61\u53bf', 'level': 3 },
          '261': { 'id': 261, 'pid': 250, 'name': '\u6c81\u53bf', 'level': 3 },
          '262': { 'id': 262, 'pid': 250, 'name': '\u6c81\u6e90\u53bf', 'level': 3 },
          '263': { 'id': 263, 'pid': 250, 'name': '\u6f5e\u57ce\u5e02', 'level': 3 }
        }
      },
      '264': {
        'id': 264,
        'pid': 220,
        'name': '\u664b\u57ce\u5e02',
        'level': 2,
        'region': {
          '265': { 'id': 265, 'pid': 264, 'name': '\u57ce\u533a', 'level': 3 },
          '266': { 'id': 266, 'pid': 264, 'name': '\u6c81\u6c34\u53bf', 'level': 3 },
          '267': { 'id': 267, 'pid': 264, 'name': '\u9633\u57ce\u53bf', 'level': 3 },
          '268': { 'id': 268, 'pid': 264, 'name': '\u9675\u5ddd\u53bf', 'level': 3 },
          '269': { 'id': 269, 'pid': 264, 'name': '\u6cfd\u5dde\u53bf', 'level': 3 },
          '270': { 'id': 270, 'pid': 264, 'name': '\u9ad8\u5e73\u5e02', 'level': 3 }
        }
      },
      '271': {
        'id': 271,
        'pid': 220,
        'name': '\u6714\u5dde\u5e02',
        'level': 2,
        'region': {
          '272': { 'id': 272, 'pid': 271, 'name': '\u6714\u57ce\u533a', 'level': 3 },
          '273': { 'id': 273, 'pid': 271, 'name': '\u5e73\u9c81\u533a', 'level': 3 },
          '274': { 'id': 274, 'pid': 271, 'name': '\u5c71\u9634\u53bf', 'level': 3 },
          '275': { 'id': 275, 'pid': 271, 'name': '\u5e94\u53bf', 'level': 3 },
          '276': { 'id': 276, 'pid': 271, 'name': '\u53f3\u7389\u53bf', 'level': 3 },
          '277': { 'id': 277, 'pid': 271, 'name': '\u6000\u4ec1\u53bf', 'level': 3 }
        }
      },
      '278': {
        'id': 278,
        'pid': 220,
        'name': '\u664b\u4e2d\u5e02',
        'level': 2,
        'region': {
          '279': { 'id': 279, 'pid': 278, 'name': '\u6986\u6b21\u533a', 'level': 3 },
          '280': { 'id': 280, 'pid': 278, 'name': '\u6986\u793e\u53bf', 'level': 3 },
          '281': { 'id': 281, 'pid': 278, 'name': '\u5de6\u6743\u53bf', 'level': 3 },
          '282': { 'id': 282, 'pid': 278, 'name': '\u548c\u987a\u53bf', 'level': 3 },
          '283': { 'id': 283, 'pid': 278, 'name': '\u6614\u9633\u53bf', 'level': 3 },
          '284': { 'id': 284, 'pid': 278, 'name': '\u5bff\u9633\u53bf', 'level': 3 },
          '285': { 'id': 285, 'pid': 278, 'name': '\u592a\u8c37\u53bf', 'level': 3 },
          '286': { 'id': 286, 'pid': 278, 'name': '\u7941\u53bf', 'level': 3 },
          '287': { 'id': 287, 'pid': 278, 'name': '\u5e73\u9065\u53bf', 'level': 3 },
          '288': { 'id': 288, 'pid': 278, 'name': '\u7075\u77f3\u53bf', 'level': 3 },
          '289': { 'id': 289, 'pid': 278, 'name': '\u4ecb\u4f11\u5e02', 'level': 3 }
        }
      },
      '290': {
        'id': 290,
        'pid': 220,
        'name': '\u8fd0\u57ce\u5e02',
        'level': 2,
        'region': {
          '291': { 'id': 291, 'pid': 290, 'name': '\u76d0\u6e56\u533a', 'level': 3 },
          '292': { 'id': 292, 'pid': 290, 'name': '\u4e34\u7317\u53bf', 'level': 3 },
          '293': { 'id': 293, 'pid': 290, 'name': '\u4e07\u8363\u53bf', 'level': 3 },
          '294': { 'id': 294, 'pid': 290, 'name': '\u95fb\u559c\u53bf', 'level': 3 },
          '295': { 'id': 295, 'pid': 290, 'name': '\u7a37\u5c71\u53bf', 'level': 3 },
          '296': { 'id': 296, 'pid': 290, 'name': '\u65b0\u7edb\u53bf', 'level': 3 },
          '297': { 'id': 297, 'pid': 290, 'name': '\u7edb\u53bf', 'level': 3 },
          '298': { 'id': 298, 'pid': 290, 'name': '\u57a3\u66f2\u53bf', 'level': 3 },
          '299': { 'id': 299, 'pid': 290, 'name': '\u590f\u53bf', 'level': 3 },
          '300': { 'id': 300, 'pid': 290, 'name': '\u5e73\u9646\u53bf', 'level': 3 },
          '301': { 'id': 301, 'pid': 290, 'name': '\u82ae\u57ce\u53bf', 'level': 3 },
          '302': { 'id': 302, 'pid': 290, 'name': '\u6c38\u6d4e\u5e02', 'level': 3 },
          '303': { 'id': 303, 'pid': 290, 'name': '\u6cb3\u6d25\u5e02', 'level': 3 }
        }
      },
      '304': {
        'id': 304,
        'pid': 220,
        'name': '\u5ffb\u5dde\u5e02',
        'level': 2,
        'region': {
          '305': { 'id': 305, 'pid': 304, 'name': '\u5ffb\u5e9c\u533a', 'level': 3 },
          '306': { 'id': 306, 'pid': 304, 'name': '\u5b9a\u8944\u53bf', 'level': 3 },
          '307': { 'id': 307, 'pid': 304, 'name': '\u4e94\u53f0\u53bf', 'level': 3 },
          '308': { 'id': 308, 'pid': 304, 'name': '\u4ee3\u53bf', 'level': 3 },
          '309': { 'id': 309, 'pid': 304, 'name': '\u7e41\u5cd9\u53bf', 'level': 3 },
          '310': { 'id': 310, 'pid': 304, 'name': '\u5b81\u6b66\u53bf', 'level': 3 },
          '311': { 'id': 311, 'pid': 304, 'name': '\u9759\u4e50\u53bf', 'level': 3 },
          '312': { 'id': 312, 'pid': 304, 'name': '\u795e\u6c60\u53bf', 'level': 3 },
          '313': { 'id': 313, 'pid': 304, 'name': '\u4e94\u5be8\u53bf', 'level': 3 },
          '314': { 'id': 314, 'pid': 304, 'name': '\u5ca2\u5c9a\u53bf', 'level': 3 },
          '315': { 'id': 315, 'pid': 304, 'name': '\u6cb3\u66f2\u53bf', 'level': 3 },
          '316': { 'id': 316, 'pid': 304, 'name': '\u4fdd\u5fb7\u53bf', 'level': 3 },
          '317': { 'id': 317, 'pid': 304, 'name': '\u504f\u5173\u53bf', 'level': 3 },
          '318': { 'id': 318, 'pid': 304, 'name': '\u539f\u5e73\u5e02', 'level': 3 }
        }
      },
      '319': {
        'id': 319, 'pid': 220, 'name': '\u4e34\u6c7e\u5e02', 'level': 2, 'region': {
          '320': { 'id': 320, 'pid': 319, 'name': '\u5c27\u90fd\u533a', 'level': 3 },
          '321': { 'id': 321, 'pid': 319, 'name': '\u66f2\u6c83\u53bf', 'level': 3 },
          '322': { 'id': 322, 'pid': 319, 'name': '\u7ffc\u57ce\u53bf', 'level': 3 },
          '323': { 'id': 323, 'pid': 319, 'name': '\u8944\u6c7e\u53bf', 'level': 3 },
          '324': { 'id': 324, 'pid': 319, 'name': '\u6d2a\u6d1e\u53bf', 'level': 3 },
          '325': { 'id': 325, 'pid': 319, 'name': '\u53e4\u53bf', 'level': 3 },
          '326': { 'id': 326, 'pid': 319, 'name': '\u5b89\u6cfd\u53bf', 'level': 3 },
          '327': { 'id': 327, 'pid': 319, 'name': '\u6d6e\u5c71\u53bf', 'level': 3 },
          '328': { 'id': 328, 'pid': 319, 'name': '\u5409\u53bf', 'level': 3 },
          '329': { 'id': 329, 'pid': 319, 'name': '\u4e61\u5b81\u53bf', 'level': 3 },
          '330': { 'id': 330, 'pid': 319, 'name': '\u5927\u5b81\u53bf', 'level': 3 },
          '331': { 'id': 331, 'pid': 319, 'name': '\u96b0\u53bf', 'level': 3 },
          '332': { 'id': 332, 'pid': 319, 'name': '\u6c38\u548c\u53bf', 'level': 3 },
          '333': { 'id': 333, 'pid': 319, 'name': '\u84b2\u53bf', 'level': 3 },
          '334': { 'id': 334, 'pid': 319, 'name': '\u6c7e\u897f\u53bf', 'level': 3 },
          '335': { 'id': 335, 'pid': 319, 'name': '\u4faf\u9a6c\u5e02', 'level': 3 },
          '336': { 'id': 336, 'pid': 319, 'name': '\u970d\u5dde\u5e02', 'level': 3 }
        }
      },
      '337': {
        'id': 337,
        'pid': 220,
        'name': '\u5415\u6881\u5e02',
        'level': 2,
        'region': {
          '338': { 'id': 338, 'pid': 337, 'name': '\u79bb\u77f3\u533a', 'level': 3 },
          '339': { 'id': 339, 'pid': 337, 'name': '\u6587\u6c34\u53bf', 'level': 3 },
          '340': { 'id': 340, 'pid': 337, 'name': '\u4ea4\u57ce\u53bf', 'level': 3 },
          '341': { 'id': 341, 'pid': 337, 'name': '\u5174\u53bf', 'level': 3 },
          '342': { 'id': 342, 'pid': 337, 'name': '\u4e34\u53bf', 'level': 3 },
          '343': { 'id': 343, 'pid': 337, 'name': '\u67f3\u6797\u53bf', 'level': 3 },
          '344': { 'id': 344, 'pid': 337, 'name': '\u77f3\u697c\u53bf', 'level': 3 },
          '345': { 'id': 345, 'pid': 337, 'name': '\u5c9a\u53bf', 'level': 3 },
          '346': { 'id': 346, 'pid': 337, 'name': '\u65b9\u5c71\u53bf', 'level': 3 },
          '347': { 'id': 347, 'pid': 337, 'name': '\u4e2d\u9633\u53bf', 'level': 3 },
          '348': { 'id': 348, 'pid': 337, 'name': '\u4ea4\u53e3\u53bf', 'level': 3 },
          '349': { 'id': 349, 'pid': 337, 'name': '\u5b5d\u4e49\u5e02', 'level': 3 },
          '350': { 'id': 350, 'pid': 337, 'name': '\u6c7e\u9633\u5e02', 'level': 3 }
        }
      }
    }
  },
  '351': {
    'id': 351, 'pid': 0, 'name': '\u5185\u8499\u53e4\u81ea\u6cbb\u533a', 'level': 1, 'city': {
      '352': {
        'id': 352,
        'pid': 351,
        'name': '\u547c\u548c\u6d69\u7279\u5e02',
        'level': 2,
        'region': {
          '353': { 'id': 353, 'pid': 352, 'name': '\u65b0\u57ce\u533a', 'level': 3 },
          '354': { 'id': 354, 'pid': 352, 'name': '\u56de\u6c11\u533a', 'level': 3 },
          '355': { 'id': 355, 'pid': 352, 'name': '\u7389\u6cc9\u533a', 'level': 3 },
          '356': { 'id': 356, 'pid': 352, 'name': '\u8d5b\u7f55\u533a', 'level': 3 },
          '357': { 'id': 357, 'pid': 352, 'name': '\u571f\u9ed8\u7279\u5de6\u65d7', 'level': 3 },
          '358': { 'id': 358, 'pid': 352, 'name': '\u6258\u514b\u6258\u53bf', 'level': 3 },
          '359': { 'id': 359, 'pid': 352, 'name': '\u548c\u6797\u683c\u5c14\u53bf', 'level': 3 },
          '360': { 'id': 360, 'pid': 352, 'name': '\u6e05\u6c34\u6cb3\u53bf', 'level': 3 },
          '361': { 'id': 361, 'pid': 352, 'name': '\u6b66\u5ddd\u53bf', 'level': 3 }
        }
      },
      '362': {
        'id': 362,
        'pid': 351,
        'name': '\u5305\u5934\u5e02',
        'level': 2,
        'region': {
          '363': { 'id': 363, 'pid': 362, 'name': '\u4e1c\u6cb3\u533a', 'level': 3 },
          '364': { 'id': 364, 'pid': 362, 'name': '\u6606\u90fd\u4ed1\u533a', 'level': 3 },
          '365': { 'id': 365, 'pid': 362, 'name': '\u9752\u5c71\u533a', 'level': 3 },
          '366': { 'id': 366, 'pid': 362, 'name': '\u77f3\u62d0\u533a', 'level': 3 },
          '367': { 'id': 367, 'pid': 362, 'name': '\u767d\u4e91\u9102\u535a\u77ff\u533a', 'level': 3 },
          '368': { 'id': 368, 'pid': 362, 'name': '\u4e5d\u539f\u533a', 'level': 3 },
          '369': { 'id': 369, 'pid': 362, 'name': '\u571f\u9ed8\u7279\u53f3\u65d7', 'level': 3 },
          '370': { 'id': 370, 'pid': 362, 'name': '\u56fa\u9633\u53bf', 'level': 3 },
          '371': { 'id': 371, 'pid': 362, 'name': '\u8fbe\u5c14\u7f55\u8302\u660e\u5b89\u8054\u5408\u65d7', 'level': 3 }
        }
      },
      '372': {
        'id': 372,
        'pid': 351,
        'name': '\u4e4c\u6d77\u5e02',
        'level': 2,
        'region': {
          '373': { 'id': 373, 'pid': 372, 'name': '\u6d77\u52c3\u6e7e\u533a', 'level': 3 },
          '374': { 'id': 374, 'pid': 372, 'name': '\u6d77\u5357\u533a', 'level': 3 },
          '375': { 'id': 375, 'pid': 372, 'name': '\u4e4c\u8fbe\u533a', 'level': 3 }
        }
      },
      '376': {
        'id': 376,
        'pid': 351,
        'name': '\u8d64\u5cf0\u5e02',
        'level': 2,
        'region': {
          '377': { 'id': 377, 'pid': 376, 'name': '\u7ea2\u5c71\u533a', 'level': 3 },
          '378': { 'id': 378, 'pid': 376, 'name': '\u5143\u5b9d\u5c71\u533a', 'level': 3 },
          '379': { 'id': 379, 'pid': 376, 'name': '\u677e\u5c71\u533a', 'level': 3 },
          '380': { 'id': 380, 'pid': 376, 'name': '\u963f\u9c81\u79d1\u5c14\u6c81\u65d7', 'level': 3 },
          '381': { 'id': 381, 'pid': 376, 'name': '\u5df4\u6797\u5de6\u65d7', 'level': 3 },
          '382': { 'id': 382, 'pid': 376, 'name': '\u5df4\u6797\u53f3\u65d7', 'level': 3 },
          '383': { 'id': 383, 'pid': 376, 'name': '\u6797\u897f\u53bf', 'level': 3 },
          '384': { 'id': 384, 'pid': 376, 'name': '\u514b\u4ec0\u514b\u817e\u65d7', 'level': 3 },
          '385': { 'id': 385, 'pid': 376, 'name': '\u7fc1\u725b\u7279\u65d7', 'level': 3 },
          '386': { 'id': 386, 'pid': 376, 'name': '\u5580\u5587\u6c81\u65d7', 'level': 3 },
          '387': { 'id': 387, 'pid': 376, 'name': '\u5b81\u57ce\u53bf', 'level': 3 },
          '388': { 'id': 388, 'pid': 376, 'name': '\u6556\u6c49\u65d7', 'level': 3 }
        }
      },
      '389': {
        'id': 389,
        'pid': 351,
        'name': '\u901a\u8fbd\u5e02',
        'level': 2,
        'region': {
          '390': { 'id': 390, 'pid': 389, 'name': '\u79d1\u5c14\u6c81\u533a', 'level': 3 },
          '391': { 'id': 391, 'pid': 389, 'name': '\u79d1\u5c14\u6c81\u5de6\u7ffc\u4e2d\u65d7', 'level': 3 },
          '392': { 'id': 392, 'pid': 389, 'name': '\u79d1\u5c14\u6c81\u5de6\u7ffc\u540e\u65d7', 'level': 3 },
          '393': { 'id': 393, 'pid': 389, 'name': '\u5f00\u9c81\u53bf', 'level': 3 },
          '394': { 'id': 394, 'pid': 389, 'name': '\u5e93\u4f26\u65d7', 'level': 3 },
          '395': { 'id': 395, 'pid': 389, 'name': '\u5948\u66fc\u65d7', 'level': 3 },
          '396': { 'id': 396, 'pid': 389, 'name': '\u624e\u9c81\u7279\u65d7', 'level': 3 },
          '397': { 'id': 397, 'pid': 389, 'name': '\u970d\u6797\u90ed\u52d2\u5e02', 'level': 3 }
        }
      },
      '398': {
        'id': 398,
        'pid': 351,
        'name': '\u9102\u5c14\u591a\u65af\u5e02',
        'level': 2,
        'region': {
          '399': { 'id': 399, 'pid': 398, 'name': '\u4e1c\u80dc\u533a', 'level': 3 },
          '400': { 'id': 400, 'pid': 398, 'name': '\u8fbe\u62c9\u7279\u65d7', 'level': 3 },
          '401': { 'id': 401, 'pid': 398, 'name': '\u51c6\u683c\u5c14\u65d7', 'level': 3 },
          '402': { 'id': 402, 'pid': 398, 'name': '\u9102\u6258\u514b\u524d\u65d7', 'level': 3 },
          '403': { 'id': 403, 'pid': 398, 'name': '\u9102\u6258\u514b\u65d7', 'level': 3 },
          '404': { 'id': 404, 'pid': 398, 'name': '\u676d\u9526\u65d7', 'level': 3 },
          '405': { 'id': 405, 'pid': 398, 'name': '\u4e4c\u5ba1\u65d7', 'level': 3 },
          '406': { 'id': 406, 'pid': 398, 'name': '\u4f0a\u91d1\u970d\u6d1b\u65d7', 'level': 3 }
        }
      },
      '407': {
        'id': 407, 'pid': 351, 'name': '\u547c\u4f26\u8d1d\u5c14\u5e02', 'level': 2, 'region': {
          '408': { 'id': 408, 'pid': 407, 'name': '\u6d77\u62c9\u5c14\u533a', 'level': 3 },
          '409': { 'id': 409, 'pid': 407, 'name': '\u624e\u8d49\u8bfa\u5c14\u533a', 'level': 3 },
          '410': { 'id': 410, 'pid': 407, 'name': '\u963f\u8363\u65d7', 'level': 3 },
          '411': {
            'id': 411,
            'pid': 407,
            'name': '\u83ab\u529b\u8fbe\u74e6\u8fbe\u65a1\u5c14\u65cf\u81ea\u6cbb\u65d7',
            'level': 3
          },
          '412': { 'id': 412, 'pid': 407, 'name': '\u9102\u4f26\u6625\u81ea\u6cbb\u65d7', 'level': 3 },
          '413': { 'id': 413, 'pid': 407, 'name': '\u9102\u6e29\u514b\u65cf\u81ea\u6cbb\u65d7', 'level': 3 },
          '414': { 'id': 414, 'pid': 407, 'name': '\u9648\u5df4\u5c14\u864e\u65d7', 'level': 3 },
          '415': { 'id': 415, 'pid': 407, 'name': '\u65b0\u5df4\u5c14\u864e\u5de6\u65d7', 'level': 3 },
          '416': { 'id': 416, 'pid': 407, 'name': '\u65b0\u5df4\u5c14\u864e\u53f3\u65d7', 'level': 3 },
          '417': { 'id': 417, 'pid': 407, 'name': '\u6ee1\u6d32\u91cc\u5e02', 'level': 3 },
          '418': { 'id': 418, 'pid': 407, 'name': '\u7259\u514b\u77f3\u5e02', 'level': 3 },
          '419': { 'id': 419, 'pid': 407, 'name': '\u624e\u5170\u5c6f\u5e02', 'level': 3 },
          '420': { 'id': 420, 'pid': 407, 'name': '\u989d\u5c14\u53e4\u7eb3\u5e02', 'level': 3 },
          '421': { 'id': 421, 'pid': 407, 'name': '\u6839\u6cb3\u5e02', 'level': 3 }
        }
      },
      '422': {
        'id': 422,
        'pid': 351,
        'name': '\u5df4\u5f66\u6dd6\u5c14\u5e02',
        'level': 2,
        'region': {
          '423': { 'id': 423, 'pid': 422, 'name': '\u4e34\u6cb3\u533a', 'level': 3 },
          '424': { 'id': 424, 'pid': 422, 'name': '\u4e94\u539f\u53bf', 'level': 3 },
          '425': { 'id': 425, 'pid': 422, 'name': '\u78f4\u53e3\u53bf', 'level': 3 },
          '426': { 'id': 426, 'pid': 422, 'name': '\u4e4c\u62c9\u7279\u524d\u65d7', 'level': 3 },
          '427': { 'id': 427, 'pid': 422, 'name': '\u4e4c\u62c9\u7279\u4e2d\u65d7', 'level': 3 },
          '428': { 'id': 428, 'pid': 422, 'name': '\u4e4c\u62c9\u7279\u540e\u65d7', 'level': 3 },
          '429': { 'id': 429, 'pid': 422, 'name': '\u676d\u9526\u540e\u65d7', 'level': 3 }
        }
      },
      '430': {
        'id': 430,
        'pid': 351,
        'name': '\u4e4c\u5170\u5bdf\u5e03\u5e02',
        'level': 2,
        'region': {
          '431': { 'id': 431, 'pid': 430, 'name': '\u96c6\u5b81\u533a', 'level': 3 },
          '432': { 'id': 432, 'pid': 430, 'name': '\u5353\u8d44\u53bf', 'level': 3 },
          '433': { 'id': 433, 'pid': 430, 'name': '\u5316\u5fb7\u53bf', 'level': 3 },
          '434': { 'id': 434, 'pid': 430, 'name': '\u5546\u90fd\u53bf', 'level': 3 },
          '435': { 'id': 435, 'pid': 430, 'name': '\u5174\u548c\u53bf', 'level': 3 },
          '436': { 'id': 436, 'pid': 430, 'name': '\u51c9\u57ce\u53bf', 'level': 3 },
          '437': { 'id': 437, 'pid': 430, 'name': '\u5bdf\u54c8\u5c14\u53f3\u7ffc\u524d\u65d7', 'level': 3 },
          '438': { 'id': 438, 'pid': 430, 'name': '\u5bdf\u54c8\u5c14\u53f3\u7ffc\u4e2d\u65d7', 'level': 3 },
          '439': { 'id': 439, 'pid': 430, 'name': '\u5bdf\u54c8\u5c14\u53f3\u7ffc\u540e\u65d7', 'level': 3 },
          '440': { 'id': 440, 'pid': 430, 'name': '\u56db\u5b50\u738b\u65d7', 'level': 3 },
          '441': { 'id': 441, 'pid': 430, 'name': '\u4e30\u9547\u5e02', 'level': 3 }
        }
      },
      '442': {
        'id': 442,
        'pid': 351,
        'name': '\u5174\u5b89\u76df',
        'level': 2,
        'region': {
          '443': { 'id': 443, 'pid': 442, 'name': '\u4e4c\u5170\u6d69\u7279\u5e02', 'level': 3 },
          '444': { 'id': 444, 'pid': 442, 'name': '\u963f\u5c14\u5c71\u5e02', 'level': 3 },
          '445': { 'id': 445, 'pid': 442, 'name': '\u79d1\u5c14\u6c81\u53f3\u7ffc\u524d\u65d7', 'level': 3 },
          '446': { 'id': 446, 'pid': 442, 'name': '\u79d1\u5c14\u6c81\u53f3\u7ffc\u4e2d\u65d7', 'level': 3 },
          '447': { 'id': 447, 'pid': 442, 'name': '\u624e\u8d49\u7279\u65d7', 'level': 3 },
          '448': { 'id': 448, 'pid': 442, 'name': '\u7a81\u6cc9\u53bf', 'level': 3 }
        }
      },
      '449': {
        'id': 449,
        'pid': 351,
        'name': '\u9521\u6797\u90ed\u52d2\u76df',
        'level': 2,
        'region': {
          '450': { 'id': 450, 'pid': 449, 'name': '\u4e8c\u8fde\u6d69\u7279\u5e02', 'level': 3 },
          '451': { 'id': 451, 'pid': 449, 'name': '\u9521\u6797\u6d69\u7279\u5e02', 'level': 3 },
          '452': { 'id': 452, 'pid': 449, 'name': '\u963f\u5df4\u560e\u65d7', 'level': 3 },
          '453': { 'id': 453, 'pid': 449, 'name': '\u82cf\u5c3c\u7279\u5de6\u65d7', 'level': 3 },
          '454': { 'id': 454, 'pid': 449, 'name': '\u82cf\u5c3c\u7279\u53f3\u65d7', 'level': 3 },
          '455': { 'id': 455, 'pid': 449, 'name': '\u4e1c\u4e4c\u73e0\u7a46\u6c81\u65d7', 'level': 3 },
          '456': { 'id': 456, 'pid': 449, 'name': '\u897f\u4e4c\u73e0\u7a46\u6c81\u65d7', 'level': 3 },
          '457': { 'id': 457, 'pid': 449, 'name': '\u592a\u4ec6\u5bfa\u65d7', 'level': 3 },
          '458': { 'id': 458, 'pid': 449, 'name': '\u9576\u9ec4\u65d7', 'level': 3 },
          '459': { 'id': 459, 'pid': 449, 'name': '\u6b63\u9576\u767d\u65d7', 'level': 3 },
          '460': { 'id': 460, 'pid': 449, 'name': '\u6b63\u84dd\u65d7', 'level': 3 },
          '461': { 'id': 461, 'pid': 449, 'name': '\u591a\u4f26\u53bf', 'level': 3 }
        }
      },
      '462': {
        'id': 462,
        'pid': 351,
        'name': '\u963f\u62c9\u5584\u76df',
        'level': 2,
        'region': {
          '463': { 'id': 463, 'pid': 462, 'name': '\u963f\u62c9\u5584\u5de6\u65d7', 'level': 3 },
          '464': { 'id': 464, 'pid': 462, 'name': '\u963f\u62c9\u5584\u53f3\u65d7', 'level': 3 },
          '465': { 'id': 465, 'pid': 462, 'name': '\u989d\u6d4e\u7eb3\u65d7', 'level': 3 }
        }
      }
    }
  },
  '466': {
    'id': 466, 'pid': 0, 'name': '\u8fbd\u5b81\u7701', 'level': 1, 'city': {
      '467': {
        'id': 467,
        'pid': 466,
        'name': '\u6c88\u9633\u5e02',
        'level': 2,
        'region': {
          '468': { 'id': 468, 'pid': 467, 'name': '\u548c\u5e73\u533a', 'level': 3 },
          '469': { 'id': 469, 'pid': 467, 'name': '\u6c88\u6cb3\u533a', 'level': 3 },
          '470': { 'id': 470, 'pid': 467, 'name': '\u5927\u4e1c\u533a', 'level': 3 },
          '471': { 'id': 471, 'pid': 467, 'name': '\u7687\u59d1\u533a', 'level': 3 },
          '472': { 'id': 472, 'pid': 467, 'name': '\u94c1\u897f\u533a', 'level': 3 },
          '473': { 'id': 473, 'pid': 467, 'name': '\u82cf\u5bb6\u5c6f\u533a', 'level': 3 },
          '474': { 'id': 474, 'pid': 467, 'name': '\u6d51\u5357\u533a', 'level': 3 },
          '475': { 'id': 475, 'pid': 467, 'name': '\u6c88\u5317\u65b0\u533a', 'level': 3 },
          '476': { 'id': 476, 'pid': 467, 'name': '\u4e8e\u6d2a\u533a', 'level': 3 },
          '477': { 'id': 477, 'pid': 467, 'name': '\u8fbd\u4e2d\u53bf', 'level': 3 },
          '478': { 'id': 478, 'pid': 467, 'name': '\u5eb7\u5e73\u53bf', 'level': 3 },
          '479': { 'id': 479, 'pid': 467, 'name': '\u6cd5\u5e93\u53bf', 'level': 3 },
          '480': { 'id': 480, 'pid': 467, 'name': '\u65b0\u6c11\u5e02', 'level': 3 }
        }
      },
      '481': {
        'id': 481,
        'pid': 466,
        'name': '\u5927\u8fde\u5e02',
        'level': 2,
        'region': {
          '482': { 'id': 482, 'pid': 481, 'name': '\u4e2d\u5c71\u533a', 'level': 3 },
          '483': { 'id': 483, 'pid': 481, 'name': '\u897f\u5c97\u533a', 'level': 3 },
          '484': { 'id': 484, 'pid': 481, 'name': '\u6c99\u6cb3\u53e3\u533a', 'level': 3 },
          '485': { 'id': 485, 'pid': 481, 'name': '\u7518\u4e95\u5b50\u533a', 'level': 3 },
          '486': { 'id': 486, 'pid': 481, 'name': '\u65c5\u987a\u53e3\u533a', 'level': 3 },
          '487': { 'id': 487, 'pid': 481, 'name': '\u91d1\u5dde\u533a', 'level': 3 },
          '488': { 'id': 488, 'pid': 481, 'name': '\u957f\u6d77\u53bf', 'level': 3 },
          '489': { 'id': 489, 'pid': 481, 'name': '\u74e6\u623f\u5e97\u5e02', 'level': 3 },
          '490': { 'id': 490, 'pid': 481, 'name': '\u666e\u5170\u5e97\u5e02', 'level': 3 },
          '491': { 'id': 491, 'pid': 481, 'name': '\u5e84\u6cb3\u5e02', 'level': 3 }
        }
      },
      '492': {
        'id': 492,
        'pid': 466,
        'name': '\u978d\u5c71\u5e02',
        'level': 2,
        'region': {
          '493': { 'id': 493, 'pid': 492, 'name': '\u94c1\u4e1c\u533a', 'level': 3 },
          '494': { 'id': 494, 'pid': 492, 'name': '\u94c1\u897f\u533a', 'level': 3 },
          '495': { 'id': 495, 'pid': 492, 'name': '\u7acb\u5c71\u533a', 'level': 3 },
          '496': { 'id': 496, 'pid': 492, 'name': '\u5343\u5c71\u533a', 'level': 3 },
          '497': { 'id': 497, 'pid': 492, 'name': '\u53f0\u5b89\u53bf', 'level': 3 },
          '498': { 'id': 498, 'pid': 492, 'name': '\u5cab\u5ca9\u6ee1\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '499': { 'id': 499, 'pid': 492, 'name': '\u6d77\u57ce\u5e02', 'level': 3 }
        }
      },
      '500': {
        'id': 500,
        'pid': 466,
        'name': '\u629a\u987a\u5e02',
        'level': 2,
        'region': {
          '501': { 'id': 501, 'pid': 500, 'name': '\u65b0\u629a\u533a', 'level': 3 },
          '502': { 'id': 502, 'pid': 500, 'name': '\u4e1c\u6d32\u533a', 'level': 3 },
          '503': { 'id': 503, 'pid': 500, 'name': '\u671b\u82b1\u533a', 'level': 3 },
          '504': { 'id': 504, 'pid': 500, 'name': '\u987a\u57ce\u533a', 'level': 3 },
          '505': { 'id': 505, 'pid': 500, 'name': '\u629a\u987a\u53bf', 'level': 3 },
          '506': { 'id': 506, 'pid': 500, 'name': '\u65b0\u5bbe\u6ee1\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '507': { 'id': 507, 'pid': 500, 'name': '\u6e05\u539f\u6ee1\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '508': {
        'id': 508,
        'pid': 466,
        'name': '\u672c\u6eaa\u5e02',
        'level': 2,
        'region': {
          '509': { 'id': 509, 'pid': 508, 'name': '\u5e73\u5c71\u533a', 'level': 3 },
          '510': { 'id': 510, 'pid': 508, 'name': '\u6eaa\u6e56\u533a', 'level': 3 },
          '511': { 'id': 511, 'pid': 508, 'name': '\u660e\u5c71\u533a', 'level': 3 },
          '512': { 'id': 512, 'pid': 508, 'name': '\u5357\u82ac\u533a', 'level': 3 },
          '513': { 'id': 513, 'pid': 508, 'name': '\u672c\u6eaa\u6ee1\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '514': { 'id': 514, 'pid': 508, 'name': '\u6853\u4ec1\u6ee1\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '515': {
        'id': 515,
        'pid': 466,
        'name': '\u4e39\u4e1c\u5e02',
        'level': 2,
        'region': {
          '516': { 'id': 516, 'pid': 515, 'name': '\u5143\u5b9d\u533a', 'level': 3 },
          '517': { 'id': 517, 'pid': 515, 'name': '\u632f\u5174\u533a', 'level': 3 },
          '518': { 'id': 518, 'pid': 515, 'name': '\u632f\u5b89\u533a', 'level': 3 },
          '519': { 'id': 519, 'pid': 515, 'name': '\u5bbd\u7538\u6ee1\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '520': { 'id': 520, 'pid': 515, 'name': '\u4e1c\u6e2f\u5e02', 'level': 3 },
          '521': { 'id': 521, 'pid': 515, 'name': '\u51e4\u57ce\u5e02', 'level': 3 }
        }
      },
      '522': {
        'id': 522,
        'pid': 466,
        'name': '\u9526\u5dde\u5e02',
        'level': 2,
        'region': {
          '523': { 'id': 523, 'pid': 522, 'name': '\u53e4\u5854\u533a', 'level': 3 },
          '524': { 'id': 524, 'pid': 522, 'name': '\u51cc\u6cb3\u533a', 'level': 3 },
          '525': { 'id': 525, 'pid': 522, 'name': '\u592a\u548c\u533a', 'level': 3 },
          '526': { 'id': 526, 'pid': 522, 'name': '\u9ed1\u5c71\u53bf', 'level': 3 },
          '527': { 'id': 527, 'pid': 522, 'name': '\u4e49\u53bf', 'level': 3 },
          '528': { 'id': 528, 'pid': 522, 'name': '\u51cc\u6d77\u5e02', 'level': 3 },
          '529': { 'id': 529, 'pid': 522, 'name': '\u5317\u9547\u5e02', 'level': 3 }
        }
      },
      '530': {
        'id': 530,
        'pid': 466,
        'name': '\u8425\u53e3\u5e02',
        'level': 2,
        'region': {
          '531': { 'id': 531, 'pid': 530, 'name': '\u7ad9\u524d\u533a', 'level': 3 },
          '532': { 'id': 532, 'pid': 530, 'name': '\u897f\u5e02\u533a', 'level': 3 },
          '533': { 'id': 533, 'pid': 530, 'name': '\u9c85\u9c7c\u5708\u533a', 'level': 3 },
          '534': { 'id': 534, 'pid': 530, 'name': '\u8001\u8fb9\u533a', 'level': 3 },
          '535': { 'id': 535, 'pid': 530, 'name': '\u76d6\u5dde\u5e02', 'level': 3 },
          '536': { 'id': 536, 'pid': 530, 'name': '\u5927\u77f3\u6865\u5e02', 'level': 3 }
        }
      },
      '537': {
        'id': 537,
        'pid': 466,
        'name': '\u961c\u65b0\u5e02',
        'level': 2,
        'region': {
          '538': { 'id': 538, 'pid': 537, 'name': '\u6d77\u5dde\u533a', 'level': 3 },
          '539': { 'id': 539, 'pid': 537, 'name': '\u65b0\u90b1\u533a', 'level': 3 },
          '540': { 'id': 540, 'pid': 537, 'name': '\u592a\u5e73\u533a', 'level': 3 },
          '541': { 'id': 541, 'pid': 537, 'name': '\u6e05\u6cb3\u95e8\u533a', 'level': 3 },
          '542': { 'id': 542, 'pid': 537, 'name': '\u7ec6\u6cb3\u533a', 'level': 3 },
          '543': { 'id': 543, 'pid': 537, 'name': '\u961c\u65b0\u8499\u53e4\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '544': { 'id': 544, 'pid': 537, 'name': '\u5f70\u6b66\u53bf', 'level': 3 }
        }
      },
      '545': {
        'id': 545,
        'pid': 466,
        'name': '\u8fbd\u9633\u5e02',
        'level': 2,
        'region': {
          '546': { 'id': 546, 'pid': 545, 'name': '\u767d\u5854\u533a', 'level': 3 },
          '547': { 'id': 547, 'pid': 545, 'name': '\u6587\u5723\u533a', 'level': 3 },
          '548': { 'id': 548, 'pid': 545, 'name': '\u5b8f\u4f1f\u533a', 'level': 3 },
          '549': { 'id': 549, 'pid': 545, 'name': '\u5f13\u957f\u5cad\u533a', 'level': 3 },
          '550': { 'id': 550, 'pid': 545, 'name': '\u592a\u5b50\u6cb3\u533a', 'level': 3 },
          '551': { 'id': 551, 'pid': 545, 'name': '\u8fbd\u9633\u53bf', 'level': 3 },
          '552': { 'id': 552, 'pid': 545, 'name': '\u706f\u5854\u5e02', 'level': 3 }
        }
      },
      '553': {
        'id': 553,
        'pid': 466,
        'name': '\u76d8\u9526\u5e02',
        'level': 2,
        'region': {
          '554': { 'id': 554, 'pid': 553, 'name': '\u53cc\u53f0\u5b50\u533a', 'level': 3 },
          '555': { 'id': 555, 'pid': 553, 'name': '\u5174\u9686\u53f0\u533a', 'level': 3 },
          '556': { 'id': 556, 'pid': 553, 'name': '\u5927\u6d3c\u53bf', 'level': 3 },
          '557': { 'id': 557, 'pid': 553, 'name': '\u76d8\u5c71\u53bf', 'level': 3 }
        }
      },
      '558': {
        'id': 558,
        'pid': 466,
        'name': '\u94c1\u5cad\u5e02',
        'level': 2,
        'region': {
          '559': { 'id': 559, 'pid': 558, 'name': '\u94f6\u5dde\u533a', 'level': 3 },
          '560': { 'id': 560, 'pid': 558, 'name': '\u6e05\u6cb3\u533a', 'level': 3 },
          '561': { 'id': 561, 'pid': 558, 'name': '\u94c1\u5cad\u53bf', 'level': 3 },
          '562': { 'id': 562, 'pid': 558, 'name': '\u897f\u4e30\u53bf', 'level': 3 },
          '563': { 'id': 563, 'pid': 558, 'name': '\u660c\u56fe\u53bf', 'level': 3 },
          '564': { 'id': 564, 'pid': 558, 'name': '\u8c03\u5175\u5c71\u5e02', 'level': 3 },
          '565': { 'id': 565, 'pid': 558, 'name': '\u5f00\u539f\u5e02', 'level': 3 }
        }
      },
      '566': {
        'id': 566,
        'pid': 466,
        'name': '\u671d\u9633\u5e02',
        'level': 2,
        'region': {
          '567': { 'id': 567, 'pid': 566, 'name': '\u53cc\u5854\u533a', 'level': 3 },
          '568': { 'id': 568, 'pid': 566, 'name': '\u9f99\u57ce\u533a', 'level': 3 },
          '569': { 'id': 569, 'pid': 566, 'name': '\u671d\u9633\u53bf', 'level': 3 },
          '570': { 'id': 570, 'pid': 566, 'name': '\u5efa\u5e73\u53bf', 'level': 3 },
          '571': {
            'id': 571,
            'pid': 566,
            'name': '\u5580\u5587\u6c81\u5de6\u7ffc\u8499\u53e4\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '572': { 'id': 572, 'pid': 566, 'name': '\u5317\u7968\u5e02', 'level': 3 },
          '573': { 'id': 573, 'pid': 566, 'name': '\u51cc\u6e90\u5e02', 'level': 3 }
        }
      },
      '574': {
        'id': 574,
        'pid': 466,
        'name': '\u846b\u82a6\u5c9b\u5e02',
        'level': 2,
        'region': {
          '575': { 'id': 575, 'pid': 574, 'name': '\u8fde\u5c71\u533a', 'level': 3 },
          '576': { 'id': 576, 'pid': 574, 'name': '\u9f99\u6e2f\u533a', 'level': 3 },
          '577': { 'id': 577, 'pid': 574, 'name': '\u5357\u7968\u533a', 'level': 3 },
          '578': { 'id': 578, 'pid': 574, 'name': '\u7ee5\u4e2d\u53bf', 'level': 3 },
          '579': { 'id': 579, 'pid': 574, 'name': '\u5efa\u660c\u53bf', 'level': 3 },
          '580': { 'id': 580, 'pid': 574, 'name': '\u5174\u57ce\u5e02', 'level': 3 }
        }
      },
      '581': {
        'id': 581,
        'pid': 466,
        'name': '\u91d1\u666e\u65b0\u533a',
        'level': 2,
        'region': {
          '582': { 'id': 582, 'pid': 581, 'name': '\u91d1\u5dde\u65b0\u533a', 'level': 3 },
          '583': { 'id': 583, 'pid': 581, 'name': '\u666e\u6e7e\u65b0\u533a', 'level': 3 },
          '584': { 'id': 584, 'pid': 581, 'name': '\u4fdd\u7a0e\u533a', 'level': 3 }
        }
      }
    }
  },
  '585': {
    'id': 585, 'pid': 0, 'name': '\u5409\u6797\u7701', 'level': 1, 'city': {
      '586': {
        'id': 586,
        'pid': 585,
        'name': '\u957f\u6625\u5e02',
        'level': 2,
        'region': {
          '587': { 'id': 587, 'pid': 586, 'name': '\u5357\u5173\u533a', 'level': 3 },
          '588': { 'id': 588, 'pid': 586, 'name': '\u5bbd\u57ce\u533a', 'level': 3 },
          '589': { 'id': 589, 'pid': 586, 'name': '\u671d\u9633\u533a', 'level': 3 },
          '590': { 'id': 590, 'pid': 586, 'name': '\u4e8c\u9053\u533a', 'level': 3 },
          '591': { 'id': 591, 'pid': 586, 'name': '\u7eff\u56ed\u533a', 'level': 3 },
          '592': { 'id': 592, 'pid': 586, 'name': '\u53cc\u9633\u533a', 'level': 3 },
          '593': { 'id': 593, 'pid': 586, 'name': '\u4e5d\u53f0\u533a', 'level': 3 },
          '594': { 'id': 594, 'pid': 586, 'name': '\u519c\u5b89\u53bf', 'level': 3 },
          '595': { 'id': 595, 'pid': 586, 'name': '\u6986\u6811\u5e02', 'level': 3 },
          '596': { 'id': 596, 'pid': 586, 'name': '\u5fb7\u60e0\u5e02', 'level': 3 }
        }
      },
      '597': {
        'id': 597,
        'pid': 585,
        'name': '\u5409\u6797\u5e02',
        'level': 2,
        'region': {
          '598': { 'id': 598, 'pid': 597, 'name': '\u660c\u9091\u533a', 'level': 3 },
          '599': { 'id': 599, 'pid': 597, 'name': '\u9f99\u6f6d\u533a', 'level': 3 },
          '600': { 'id': 600, 'pid': 597, 'name': '\u8239\u8425\u533a', 'level': 3 },
          '601': { 'id': 601, 'pid': 597, 'name': '\u4e30\u6ee1\u533a', 'level': 3 },
          '602': { 'id': 602, 'pid': 597, 'name': '\u6c38\u5409\u53bf', 'level': 3 },
          '603': { 'id': 603, 'pid': 597, 'name': '\u86df\u6cb3\u5e02', 'level': 3 },
          '604': { 'id': 604, 'pid': 597, 'name': '\u6866\u7538\u5e02', 'level': 3 },
          '605': { 'id': 605, 'pid': 597, 'name': '\u8212\u5170\u5e02', 'level': 3 },
          '606': { 'id': 606, 'pid': 597, 'name': '\u78d0\u77f3\u5e02', 'level': 3 }
        }
      },
      '607': {
        'id': 607,
        'pid': 585,
        'name': '\u56db\u5e73\u5e02',
        'level': 2,
        'region': {
          '608': { 'id': 608, 'pid': 607, 'name': '\u94c1\u897f\u533a', 'level': 3 },
          '609': { 'id': 609, 'pid': 607, 'name': '\u94c1\u4e1c\u533a', 'level': 3 },
          '610': { 'id': 610, 'pid': 607, 'name': '\u68a8\u6811\u53bf', 'level': 3 },
          '611': { 'id': 611, 'pid': 607, 'name': '\u4f0a\u901a\u6ee1\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '612': { 'id': 612, 'pid': 607, 'name': '\u516c\u4e3b\u5cad\u5e02', 'level': 3 },
          '613': { 'id': 613, 'pid': 607, 'name': '\u53cc\u8fbd\u5e02', 'level': 3 }
        }
      },
      '614': {
        'id': 614,
        'pid': 585,
        'name': '\u8fbd\u6e90\u5e02',
        'level': 2,
        'region': {
          '615': { 'id': 615, 'pid': 614, 'name': '\u9f99\u5c71\u533a', 'level': 3 },
          '616': { 'id': 616, 'pid': 614, 'name': '\u897f\u5b89\u533a', 'level': 3 },
          '617': { 'id': 617, 'pid': 614, 'name': '\u4e1c\u4e30\u53bf', 'level': 3 },
          '618': { 'id': 618, 'pid': 614, 'name': '\u4e1c\u8fbd\u53bf', 'level': 3 }
        }
      },
      '619': {
        'id': 619,
        'pid': 585,
        'name': '\u901a\u5316\u5e02',
        'level': 2,
        'region': {
          '620': { 'id': 620, 'pid': 619, 'name': '\u4e1c\u660c\u533a', 'level': 3 },
          '621': { 'id': 621, 'pid': 619, 'name': '\u4e8c\u9053\u6c5f\u533a', 'level': 3 },
          '622': { 'id': 622, 'pid': 619, 'name': '\u901a\u5316\u53bf', 'level': 3 },
          '623': { 'id': 623, 'pid': 619, 'name': '\u8f89\u5357\u53bf', 'level': 3 },
          '624': { 'id': 624, 'pid': 619, 'name': '\u67f3\u6cb3\u53bf', 'level': 3 },
          '625': { 'id': 625, 'pid': 619, 'name': '\u6885\u6cb3\u53e3\u5e02', 'level': 3 },
          '626': { 'id': 626, 'pid': 619, 'name': '\u96c6\u5b89\u5e02', 'level': 3 }
        }
      },
      '627': {
        'id': 627,
        'pid': 585,
        'name': '\u767d\u5c71\u5e02',
        'level': 2,
        'region': {
          '628': { 'id': 628, 'pid': 627, 'name': '\u6d51\u6c5f\u533a', 'level': 3 },
          '629': { 'id': 629, 'pid': 627, 'name': '\u6c5f\u6e90\u533a', 'level': 3 },
          '630': { 'id': 630, 'pid': 627, 'name': '\u629a\u677e\u53bf', 'level': 3 },
          '631': { 'id': 631, 'pid': 627, 'name': '\u9756\u5b87\u53bf', 'level': 3 },
          '632': { 'id': 632, 'pid': 627, 'name': '\u957f\u767d\u671d\u9c9c\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '633': { 'id': 633, 'pid': 627, 'name': '\u4e34\u6c5f\u5e02', 'level': 3 }
        }
      },
      '634': {
        'id': 634,
        'pid': 585,
        'name': '\u677e\u539f\u5e02',
        'level': 2,
        'region': {
          '635': { 'id': 635, 'pid': 634, 'name': '\u5b81\u6c5f\u533a', 'level': 3 },
          '636': {
            'id': 636,
            'pid': 634,
            'name': '\u524d\u90ed\u5c14\u7f57\u65af\u8499\u53e4\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '637': { 'id': 637, 'pid': 634, 'name': '\u957f\u5cad\u53bf', 'level': 3 },
          '638': { 'id': 638, 'pid': 634, 'name': '\u4e7e\u5b89\u53bf', 'level': 3 },
          '639': { 'id': 639, 'pid': 634, 'name': '\u6276\u4f59\u5e02', 'level': 3 }
        }
      },
      '640': {
        'id': 640,
        'pid': 585,
        'name': '\u767d\u57ce\u5e02',
        'level': 2,
        'region': {
          '641': { 'id': 641, 'pid': 640, 'name': '\u6d2e\u5317\u533a', 'level': 3 },
          '642': { 'id': 642, 'pid': 640, 'name': '\u9547\u8d49\u53bf', 'level': 3 },
          '643': { 'id': 643, 'pid': 640, 'name': '\u901a\u6986\u53bf', 'level': 3 },
          '644': { 'id': 644, 'pid': 640, 'name': '\u6d2e\u5357\u5e02', 'level': 3 },
          '645': { 'id': 645, 'pid': 640, 'name': '\u5927\u5b89\u5e02', 'level': 3 }
        }
      },
      '646': {
        'id': 646,
        'pid': 585,
        'name': '\u5ef6\u8fb9\u671d\u9c9c\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '647': { 'id': 647, 'pid': 646, 'name': '\u5ef6\u5409\u5e02', 'level': 3 },
          '648': { 'id': 648, 'pid': 646, 'name': '\u56fe\u4eec\u5e02', 'level': 3 },
          '649': { 'id': 649, 'pid': 646, 'name': '\u6566\u5316\u5e02', 'level': 3 },
          '650': { 'id': 650, 'pid': 646, 'name': '\u73f2\u6625\u5e02', 'level': 3 },
          '651': { 'id': 651, 'pid': 646, 'name': '\u9f99\u4e95\u5e02', 'level': 3 },
          '652': { 'id': 652, 'pid': 646, 'name': '\u548c\u9f99\u5e02', 'level': 3 },
          '653': { 'id': 653, 'pid': 646, 'name': '\u6c6a\u6e05\u53bf', 'level': 3 },
          '654': { 'id': 654, 'pid': 646, 'name': '\u5b89\u56fe\u53bf', 'level': 3 }
        }
      }
    }
  },
  '655': {
    'id': 655, 'pid': 0, 'name': '\u9ed1\u9f99\u6c5f\u7701', 'level': 1, 'city': {
      '656': {
        'id': 656, 'pid': 655, 'name': '\u54c8\u5c14\u6ee8\u5e02', 'level': 2, 'region': {
          '657': { 'id': 657, 'pid': 656, 'name': '\u9053\u91cc\u533a', 'level': 3 },
          '658': { 'id': 658, 'pid': 656, 'name': '\u5357\u5c97\u533a', 'level': 3 },
          '659': { 'id': 659, 'pid': 656, 'name': '\u9053\u5916\u533a', 'level': 3 },
          '660': { 'id': 660, 'pid': 656, 'name': '\u5e73\u623f\u533a', 'level': 3 },
          '661': { 'id': 661, 'pid': 656, 'name': '\u677e\u5317\u533a', 'level': 3 },
          '662': { 'id': 662, 'pid': 656, 'name': '\u9999\u574a\u533a', 'level': 3 },
          '663': { 'id': 663, 'pid': 656, 'name': '\u547c\u5170\u533a', 'level': 3 },
          '664': { 'id': 664, 'pid': 656, 'name': '\u963f\u57ce\u533a', 'level': 3 },
          '665': { 'id': 665, 'pid': 656, 'name': '\u53cc\u57ce\u533a', 'level': 3 },
          '666': { 'id': 666, 'pid': 656, 'name': '\u4f9d\u5170\u53bf', 'level': 3 },
          '667': { 'id': 667, 'pid': 656, 'name': '\u65b9\u6b63\u53bf', 'level': 3 },
          '668': { 'id': 668, 'pid': 656, 'name': '\u5bbe\u53bf', 'level': 3 },
          '669': { 'id': 669, 'pid': 656, 'name': '\u5df4\u5f66\u53bf', 'level': 3 },
          '670': { 'id': 670, 'pid': 656, 'name': '\u6728\u5170\u53bf', 'level': 3 },
          '671': { 'id': 671, 'pid': 656, 'name': '\u901a\u6cb3\u53bf', 'level': 3 },
          '672': { 'id': 672, 'pid': 656, 'name': '\u5ef6\u5bff\u53bf', 'level': 3 },
          '673': { 'id': 673, 'pid': 656, 'name': '\u5c1a\u5fd7\u5e02', 'level': 3 },
          '674': { 'id': 674, 'pid': 656, 'name': '\u4e94\u5e38\u5e02', 'level': 3 }
        }
      },
      '675': {
        'id': 675, 'pid': 655, 'name': '\u9f50\u9f50\u54c8\u5c14\u5e02', 'level': 2, 'region': {
          '676': { 'id': 676, 'pid': 675, 'name': '\u9f99\u6c99\u533a', 'level': 3 },
          '677': { 'id': 677, 'pid': 675, 'name': '\u5efa\u534e\u533a', 'level': 3 },
          '678': { 'id': 678, 'pid': 675, 'name': '\u94c1\u950b\u533a', 'level': 3 },
          '679': { 'id': 679, 'pid': 675, 'name': '\u6602\u6602\u6eaa\u533a', 'level': 3 },
          '680': { 'id': 680, 'pid': 675, 'name': '\u5bcc\u62c9\u5c14\u57fa\u533a', 'level': 3 },
          '681': { 'id': 681, 'pid': 675, 'name': '\u78be\u5b50\u5c71\u533a', 'level': 3 },
          '682': { 'id': 682, 'pid': 675, 'name': '\u6885\u91cc\u65af\u8fbe\u65a1\u5c14\u65cf\u533a', 'level': 3 },
          '683': { 'id': 683, 'pid': 675, 'name': '\u9f99\u6c5f\u53bf', 'level': 3 },
          '684': { 'id': 684, 'pid': 675, 'name': '\u4f9d\u5b89\u53bf', 'level': 3 },
          '685': { 'id': 685, 'pid': 675, 'name': '\u6cf0\u6765\u53bf', 'level': 3 },
          '686': { 'id': 686, 'pid': 675, 'name': '\u7518\u5357\u53bf', 'level': 3 },
          '687': { 'id': 687, 'pid': 675, 'name': '\u5bcc\u88d5\u53bf', 'level': 3 },
          '688': { 'id': 688, 'pid': 675, 'name': '\u514b\u5c71\u53bf', 'level': 3 },
          '689': { 'id': 689, 'pid': 675, 'name': '\u514b\u4e1c\u53bf', 'level': 3 },
          '690': { 'id': 690, 'pid': 675, 'name': '\u62dc\u6cc9\u53bf', 'level': 3 },
          '691': { 'id': 691, 'pid': 675, 'name': '\u8bb7\u6cb3\u5e02', 'level': 3 }
        }
      },
      '692': {
        'id': 692,
        'pid': 655,
        'name': '\u9e21\u897f\u5e02',
        'level': 2,
        'region': {
          '693': { 'id': 693, 'pid': 692, 'name': '\u9e21\u51a0\u533a', 'level': 3 },
          '694': { 'id': 694, 'pid': 692, 'name': '\u6052\u5c71\u533a', 'level': 3 },
          '695': { 'id': 695, 'pid': 692, 'name': '\u6ef4\u9053\u533a', 'level': 3 },
          '696': { 'id': 696, 'pid': 692, 'name': '\u68a8\u6811\u533a', 'level': 3 },
          '697': { 'id': 697, 'pid': 692, 'name': '\u57ce\u5b50\u6cb3\u533a', 'level': 3 },
          '698': { 'id': 698, 'pid': 692, 'name': '\u9ebb\u5c71\u533a', 'level': 3 },
          '699': { 'id': 699, 'pid': 692, 'name': '\u9e21\u4e1c\u53bf', 'level': 3 },
          '700': { 'id': 700, 'pid': 692, 'name': '\u864e\u6797\u5e02', 'level': 3 },
          '701': { 'id': 701, 'pid': 692, 'name': '\u5bc6\u5c71\u5e02', 'level': 3 }
        }
      },
      '702': {
        'id': 702,
        'pid': 655,
        'name': '\u9e64\u5c97\u5e02',
        'level': 2,
        'region': {
          '703': { 'id': 703, 'pid': 702, 'name': '\u5411\u9633\u533a', 'level': 3 },
          '704': { 'id': 704, 'pid': 702, 'name': '\u5de5\u519c\u533a', 'level': 3 },
          '705': { 'id': 705, 'pid': 702, 'name': '\u5357\u5c71\u533a', 'level': 3 },
          '706': { 'id': 706, 'pid': 702, 'name': '\u5174\u5b89\u533a', 'level': 3 },
          '707': { 'id': 707, 'pid': 702, 'name': '\u4e1c\u5c71\u533a', 'level': 3 },
          '708': { 'id': 708, 'pid': 702, 'name': '\u5174\u5c71\u533a', 'level': 3 },
          '709': { 'id': 709, 'pid': 702, 'name': '\u841d\u5317\u53bf', 'level': 3 },
          '710': { 'id': 710, 'pid': 702, 'name': '\u7ee5\u6ee8\u53bf', 'level': 3 }
        }
      },
      '711': {
        'id': 711,
        'pid': 655,
        'name': '\u53cc\u9e2d\u5c71\u5e02',
        'level': 2,
        'region': {
          '712': { 'id': 712, 'pid': 711, 'name': '\u5c16\u5c71\u533a', 'level': 3 },
          '713': { 'id': 713, 'pid': 711, 'name': '\u5cad\u4e1c\u533a', 'level': 3 },
          '714': { 'id': 714, 'pid': 711, 'name': '\u56db\u65b9\u53f0\u533a', 'level': 3 },
          '715': { 'id': 715, 'pid': 711, 'name': '\u5b9d\u5c71\u533a', 'level': 3 },
          '716': { 'id': 716, 'pid': 711, 'name': '\u96c6\u8d24\u53bf', 'level': 3 },
          '717': { 'id': 717, 'pid': 711, 'name': '\u53cb\u8c0a\u53bf', 'level': 3 },
          '718': { 'id': 718, 'pid': 711, 'name': '\u5b9d\u6e05\u53bf', 'level': 3 },
          '719': { 'id': 719, 'pid': 711, 'name': '\u9976\u6cb3\u53bf', 'level': 3 }
        }
      },
      '720': {
        'id': 720,
        'pid': 655,
        'name': '\u5927\u5e86\u5e02',
        'level': 2,
        'region': {
          '721': { 'id': 721, 'pid': 720, 'name': '\u8428\u5c14\u56fe\u533a', 'level': 3 },
          '722': { 'id': 722, 'pid': 720, 'name': '\u9f99\u51e4\u533a', 'level': 3 },
          '723': { 'id': 723, 'pid': 720, 'name': '\u8ba9\u80e1\u8def\u533a', 'level': 3 },
          '724': { 'id': 724, 'pid': 720, 'name': '\u7ea2\u5c97\u533a', 'level': 3 },
          '725': { 'id': 725, 'pid': 720, 'name': '\u5927\u540c\u533a', 'level': 3 },
          '726': { 'id': 726, 'pid': 720, 'name': '\u8087\u5dde\u53bf', 'level': 3 },
          '727': { 'id': 727, 'pid': 720, 'name': '\u8087\u6e90\u53bf', 'level': 3 },
          '728': { 'id': 728, 'pid': 720, 'name': '\u6797\u7538\u53bf', 'level': 3 },
          '729': {
            'id': 729,
            'pid': 720,
            'name': '\u675c\u5c14\u4f2f\u7279\u8499\u53e4\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          }
        }
      },
      '730': {
        'id': 730, 'pid': 655, 'name': '\u4f0a\u6625\u5e02', 'level': 2, 'region': {
          '731': { 'id': 731, 'pid': 730, 'name': '\u4f0a\u6625\u533a', 'level': 3 },
          '732': { 'id': 732, 'pid': 730, 'name': '\u5357\u5c94\u533a', 'level': 3 },
          '733': { 'id': 733, 'pid': 730, 'name': '\u53cb\u597d\u533a', 'level': 3 },
          '734': { 'id': 734, 'pid': 730, 'name': '\u897f\u6797\u533a', 'level': 3 },
          '735': { 'id': 735, 'pid': 730, 'name': '\u7fe0\u5ce6\u533a', 'level': 3 },
          '736': { 'id': 736, 'pid': 730, 'name': '\u65b0\u9752\u533a', 'level': 3 },
          '737': { 'id': 737, 'pid': 730, 'name': '\u7f8e\u6eaa\u533a', 'level': 3 },
          '738': { 'id': 738, 'pid': 730, 'name': '\u91d1\u5c71\u5c6f\u533a', 'level': 3 },
          '739': { 'id': 739, 'pid': 730, 'name': '\u4e94\u8425\u533a', 'level': 3 },
          '740': { 'id': 740, 'pid': 730, 'name': '\u4e4c\u9a6c\u6cb3\u533a', 'level': 3 },
          '741': { 'id': 741, 'pid': 730, 'name': '\u6c64\u65fa\u6cb3\u533a', 'level': 3 },
          '742': { 'id': 742, 'pid': 730, 'name': '\u5e26\u5cad\u533a', 'level': 3 },
          '743': { 'id': 743, 'pid': 730, 'name': '\u4e4c\u4f0a\u5cad\u533a', 'level': 3 },
          '744': { 'id': 744, 'pid': 730, 'name': '\u7ea2\u661f\u533a', 'level': 3 },
          '745': { 'id': 745, 'pid': 730, 'name': '\u4e0a\u7518\u5cad\u533a', 'level': 3 },
          '746': { 'id': 746, 'pid': 730, 'name': '\u5609\u836b\u53bf', 'level': 3 },
          '747': { 'id': 747, 'pid': 730, 'name': '\u94c1\u529b\u5e02', 'level': 3 }
        }
      },
      '748': {
        'id': 748,
        'pid': 655,
        'name': '\u4f73\u6728\u65af\u5e02',
        'level': 2,
        'region': {
          '749': { 'id': 749, 'pid': 748, 'name': '\u5411\u9633\u533a', 'level': 3 },
          '750': { 'id': 750, 'pid': 748, 'name': '\u524d\u8fdb\u533a', 'level': 3 },
          '751': { 'id': 751, 'pid': 748, 'name': '\u4e1c\u98ce\u533a', 'level': 3 },
          '752': { 'id': 752, 'pid': 748, 'name': '\u90ca\u533a', 'level': 3 },
          '753': { 'id': 753, 'pid': 748, 'name': '\u6866\u5357\u53bf', 'level': 3 },
          '754': { 'id': 754, 'pid': 748, 'name': '\u6866\u5ddd\u53bf', 'level': 3 },
          '755': { 'id': 755, 'pid': 748, 'name': '\u6c64\u539f\u53bf', 'level': 3 },
          '756': { 'id': 756, 'pid': 748, 'name': '\u629a\u8fdc\u53bf', 'level': 3 },
          '757': { 'id': 757, 'pid': 748, 'name': '\u540c\u6c5f\u5e02', 'level': 3 },
          '758': { 'id': 758, 'pid': 748, 'name': '\u5bcc\u9526\u5e02', 'level': 3 }
        }
      },
      '759': {
        'id': 759,
        'pid': 655,
        'name': '\u4e03\u53f0\u6cb3\u5e02',
        'level': 2,
        'region': {
          '760': { 'id': 760, 'pid': 759, 'name': '\u65b0\u5174\u533a', 'level': 3 },
          '761': { 'id': 761, 'pid': 759, 'name': '\u6843\u5c71\u533a', 'level': 3 },
          '762': { 'id': 762, 'pid': 759, 'name': '\u8304\u5b50\u6cb3\u533a', 'level': 3 },
          '763': { 'id': 763, 'pid': 759, 'name': '\u52c3\u5229\u53bf', 'level': 3 }
        }
      },
      '764': {
        'id': 764,
        'pid': 655,
        'name': '\u7261\u4e39\u6c5f\u5e02',
        'level': 2,
        'region': {
          '765': { 'id': 765, 'pid': 764, 'name': '\u4e1c\u5b89\u533a', 'level': 3 },
          '766': { 'id': 766, 'pid': 764, 'name': '\u9633\u660e\u533a', 'level': 3 },
          '767': { 'id': 767, 'pid': 764, 'name': '\u7231\u6c11\u533a', 'level': 3 },
          '768': { 'id': 768, 'pid': 764, 'name': '\u897f\u5b89\u533a', 'level': 3 },
          '769': { 'id': 769, 'pid': 764, 'name': '\u4e1c\u5b81\u53bf', 'level': 3 },
          '770': { 'id': 770, 'pid': 764, 'name': '\u6797\u53e3\u53bf', 'level': 3 },
          '771': { 'id': 771, 'pid': 764, 'name': '\u7ee5\u82ac\u6cb3\u5e02', 'level': 3 },
          '772': { 'id': 772, 'pid': 764, 'name': '\u6d77\u6797\u5e02', 'level': 3 },
          '773': { 'id': 773, 'pid': 764, 'name': '\u5b81\u5b89\u5e02', 'level': 3 },
          '774': { 'id': 774, 'pid': 764, 'name': '\u7a46\u68f1\u5e02', 'level': 3 }
        }
      },
      '775': {
        'id': 775,
        'pid': 655,
        'name': '\u9ed1\u6cb3\u5e02',
        'level': 2,
        'region': {
          '776': { 'id': 776, 'pid': 775, 'name': '\u7231\u8f89\u533a', 'level': 3 },
          '777': { 'id': 777, 'pid': 775, 'name': '\u5ae9\u6c5f\u53bf', 'level': 3 },
          '778': { 'id': 778, 'pid': 775, 'name': '\u900a\u514b\u53bf', 'level': 3 },
          '779': { 'id': 779, 'pid': 775, 'name': '\u5b59\u5434\u53bf', 'level': 3 },
          '780': { 'id': 780, 'pid': 775, 'name': '\u5317\u5b89\u5e02', 'level': 3 },
          '781': { 'id': 781, 'pid': 775, 'name': '\u4e94\u5927\u8fde\u6c60\u5e02', 'level': 3 }
        }
      },
      '782': {
        'id': 782,
        'pid': 655,
        'name': '\u7ee5\u5316\u5e02',
        'level': 2,
        'region': {
          '783': { 'id': 783, 'pid': 782, 'name': '\u5317\u6797\u533a', 'level': 3 },
          '784': { 'id': 784, 'pid': 782, 'name': '\u671b\u594e\u53bf', 'level': 3 },
          '785': { 'id': 785, 'pid': 782, 'name': '\u5170\u897f\u53bf', 'level': 3 },
          '786': { 'id': 786, 'pid': 782, 'name': '\u9752\u5188\u53bf', 'level': 3 },
          '787': { 'id': 787, 'pid': 782, 'name': '\u5e86\u5b89\u53bf', 'level': 3 },
          '788': { 'id': 788, 'pid': 782, 'name': '\u660e\u6c34\u53bf', 'level': 3 },
          '789': { 'id': 789, 'pid': 782, 'name': '\u7ee5\u68f1\u53bf', 'level': 3 },
          '790': { 'id': 790, 'pid': 782, 'name': '\u5b89\u8fbe\u5e02', 'level': 3 },
          '791': { 'id': 791, 'pid': 782, 'name': '\u8087\u4e1c\u5e02', 'level': 3 },
          '792': { 'id': 792, 'pid': 782, 'name': '\u6d77\u4f26\u5e02', 'level': 3 }
        }
      },
      '793': {
        'id': 793,
        'pid': 655,
        'name': '\u5927\u5174\u5b89\u5cad\u5730\u533a',
        'level': 2,
        'region': {
          '794': { 'id': 794, 'pid': 793, 'name': '\u52a0\u683c\u8fbe\u5947\u533a', 'level': 3 },
          '795': { 'id': 795, 'pid': 793, 'name': '\u65b0\u6797\u533a', 'level': 3 },
          '796': { 'id': 796, 'pid': 793, 'name': '\u677e\u5cad\u533a', 'level': 3 },
          '797': { 'id': 797, 'pid': 793, 'name': '\u547c\u4e2d\u533a', 'level': 3 },
          '798': { 'id': 798, 'pid': 793, 'name': '\u547c\u739b\u53bf', 'level': 3 },
          '799': { 'id': 799, 'pid': 793, 'name': '\u5854\u6cb3\u53bf', 'level': 3 },
          '800': { 'id': 800, 'pid': 793, 'name': '\u6f20\u6cb3\u53bf', 'level': 3 }
        }
      }
    }
  },
  '801': {
    'id': 801, 'pid': 0, 'name': '\u4e0a\u6d77\u5e02', 'level': 1, 'city': {
      '802': {
        'id': 802, 'pid': 801, 'name': '\u4e0a\u6d77\u5e02', 'level': 2, 'region': {
          '803': { 'id': 803, 'pid': 802, 'name': '\u9ec4\u6d66\u533a', 'level': 3 },
          '804': { 'id': 804, 'pid': 802, 'name': '\u5f90\u6c47\u533a', 'level': 3 },
          '805': { 'id': 805, 'pid': 802, 'name': '\u957f\u5b81\u533a', 'level': 3 },
          '806': { 'id': 806, 'pid': 802, 'name': '\u9759\u5b89\u533a', 'level': 3 },
          '807': { 'id': 807, 'pid': 802, 'name': '\u666e\u9640\u533a', 'level': 3 },
          '808': { 'id': 808, 'pid': 802, 'name': '\u95f8\u5317\u533a', 'level': 3 },
          '809': { 'id': 809, 'pid': 802, 'name': '\u8679\u53e3\u533a', 'level': 3 },
          '810': { 'id': 810, 'pid': 802, 'name': '\u6768\u6d66\u533a', 'level': 3 },
          '811': { 'id': 811, 'pid': 802, 'name': '\u95f5\u884c\u533a', 'level': 3 },
          '812': { 'id': 812, 'pid': 802, 'name': '\u5b9d\u5c71\u533a', 'level': 3 },
          '813': { 'id': 813, 'pid': 802, 'name': '\u5609\u5b9a\u533a', 'level': 3 },
          '814': { 'id': 814, 'pid': 802, 'name': '\u6d66\u4e1c\u65b0\u533a', 'level': 3 },
          '815': { 'id': 815, 'pid': 802, 'name': '\u91d1\u5c71\u533a', 'level': 3 },
          '816': { 'id': 816, 'pid': 802, 'name': '\u677e\u6c5f\u533a', 'level': 3 },
          '817': { 'id': 817, 'pid': 802, 'name': '\u9752\u6d66\u533a', 'level': 3 },
          '818': { 'id': 818, 'pid': 802, 'name': '\u5949\u8d24\u533a', 'level': 3 },
          '819': { 'id': 819, 'pid': 802, 'name': '\u5d07\u660e\u53bf', 'level': 3 }
        }
      }
    }
  },
  '820': {
    'id': 820, 'pid': 0, 'name': '\u6c5f\u82cf\u7701', 'level': 1, 'city': {
      '821': {
        'id': 821,
        'pid': 820,
        'name': '\u5357\u4eac\u5e02',
        'level': 2,
        'region': {
          '822': { 'id': 822, 'pid': 821, 'name': '\u7384\u6b66\u533a', 'level': 3 },
          '823': { 'id': 823, 'pid': 821, 'name': '\u79e6\u6dee\u533a', 'level': 3 },
          '824': { 'id': 824, 'pid': 821, 'name': '\u5efa\u90ba\u533a', 'level': 3 },
          '825': { 'id': 825, 'pid': 821, 'name': '\u9f13\u697c\u533a', 'level': 3 },
          '826': { 'id': 826, 'pid': 821, 'name': '\u6d66\u53e3\u533a', 'level': 3 },
          '827': { 'id': 827, 'pid': 821, 'name': '\u6816\u971e\u533a', 'level': 3 },
          '828': { 'id': 828, 'pid': 821, 'name': '\u96e8\u82b1\u53f0\u533a', 'level': 3 },
          '829': { 'id': 829, 'pid': 821, 'name': '\u6c5f\u5b81\u533a', 'level': 3 },
          '830': { 'id': 830, 'pid': 821, 'name': '\u516d\u5408\u533a', 'level': 3 },
          '831': { 'id': 831, 'pid': 821, 'name': '\u6ea7\u6c34\u533a', 'level': 3 },
          '832': { 'id': 832, 'pid': 821, 'name': '\u9ad8\u6df3\u533a', 'level': 3 }
        }
      },
      '833': {
        'id': 833,
        'pid': 820,
        'name': '\u65e0\u9521\u5e02',
        'level': 2,
        'region': {
          '834': { 'id': 834, 'pid': 833, 'name': '\u5d07\u5b89\u533a', 'level': 3 },
          '835': { 'id': 835, 'pid': 833, 'name': '\u5357\u957f\u533a', 'level': 3 },
          '836': { 'id': 836, 'pid': 833, 'name': '\u5317\u5858\u533a', 'level': 3 },
          '837': { 'id': 837, 'pid': 833, 'name': '\u9521\u5c71\u533a', 'level': 3 },
          '838': { 'id': 838, 'pid': 833, 'name': '\u60e0\u5c71\u533a', 'level': 3 },
          '839': { 'id': 839, 'pid': 833, 'name': '\u6ee8\u6e56\u533a', 'level': 3 },
          '840': { 'id': 840, 'pid': 833, 'name': '\u6c5f\u9634\u5e02', 'level': 3 },
          '841': { 'id': 841, 'pid': 833, 'name': '\u5b9c\u5174\u5e02', 'level': 3 }
        }
      },
      '842': {
        'id': 842,
        'pid': 820,
        'name': '\u5f90\u5dde\u5e02',
        'level': 2,
        'region': {
          '843': { 'id': 843, 'pid': 842, 'name': '\u9f13\u697c\u533a', 'level': 3 },
          '844': { 'id': 844, 'pid': 842, 'name': '\u4e91\u9f99\u533a', 'level': 3 },
          '845': { 'id': 845, 'pid': 842, 'name': '\u8d3e\u6c6a\u533a', 'level': 3 },
          '846': { 'id': 846, 'pid': 842, 'name': '\u6cc9\u5c71\u533a', 'level': 3 },
          '847': { 'id': 847, 'pid': 842, 'name': '\u94dc\u5c71\u533a', 'level': 3 },
          '848': { 'id': 848, 'pid': 842, 'name': '\u4e30\u53bf', 'level': 3 },
          '849': { 'id': 849, 'pid': 842, 'name': '\u6c9b\u53bf', 'level': 3 },
          '850': { 'id': 850, 'pid': 842, 'name': '\u7762\u5b81\u53bf', 'level': 3 },
          '851': { 'id': 851, 'pid': 842, 'name': '\u65b0\u6c82\u5e02', 'level': 3 },
          '852': { 'id': 852, 'pid': 842, 'name': '\u90b3\u5dde\u5e02', 'level': 3 }
        }
      },
      '853': {
        'id': 853,
        'pid': 820,
        'name': '\u5e38\u5dde\u5e02',
        'level': 2,
        'region': {
          '854': { 'id': 854, 'pid': 853, 'name': '\u5929\u5b81\u533a', 'level': 3 },
          '855': { 'id': 855, 'pid': 853, 'name': '\u949f\u697c\u533a', 'level': 3 },
          '856': { 'id': 856, 'pid': 853, 'name': '\u621a\u5885\u5830\u533a', 'level': 3 },
          '857': { 'id': 857, 'pid': 853, 'name': '\u65b0\u5317\u533a', 'level': 3 },
          '858': { 'id': 858, 'pid': 853, 'name': '\u6b66\u8fdb\u533a', 'level': 3 },
          '859': { 'id': 859, 'pid': 853, 'name': '\u6ea7\u9633\u5e02', 'level': 3 },
          '860': { 'id': 860, 'pid': 853, 'name': '\u91d1\u575b\u5e02', 'level': 3 }
        }
      },
      '861': {
        'id': 861,
        'pid': 820,
        'name': '\u82cf\u5dde\u5e02',
        'level': 2,
        'region': {
          '862': { 'id': 862, 'pid': 861, 'name': '\u864e\u4e18\u533a', 'level': 3 },
          '863': { 'id': 863, 'pid': 861, 'name': '\u5434\u4e2d\u533a', 'level': 3 },
          '864': { 'id': 864, 'pid': 861, 'name': '\u76f8\u57ce\u533a', 'level': 3 },
          '865': { 'id': 865, 'pid': 861, 'name': '\u59d1\u82cf\u533a', 'level': 3 },
          '866': { 'id': 866, 'pid': 861, 'name': '\u5434\u6c5f\u533a', 'level': 3 },
          '867': { 'id': 867, 'pid': 861, 'name': '\u5e38\u719f\u5e02', 'level': 3 },
          '868': { 'id': 868, 'pid': 861, 'name': '\u5f20\u5bb6\u6e2f\u5e02', 'level': 3 },
          '869': { 'id': 869, 'pid': 861, 'name': '\u6606\u5c71\u5e02', 'level': 3 },
          '870': { 'id': 870, 'pid': 861, 'name': '\u592a\u4ed3\u5e02', 'level': 3 }
        }
      },
      '871': {
        'id': 871,
        'pid': 820,
        'name': '\u5357\u901a\u5e02',
        'level': 2,
        'region': {
          '872': { 'id': 872, 'pid': 871, 'name': '\u5d07\u5ddd\u533a', 'level': 3 },
          '873': { 'id': 873, 'pid': 871, 'name': '\u6e2f\u95f8\u533a', 'level': 3 },
          '874': { 'id': 874, 'pid': 871, 'name': '\u901a\u5dde\u533a', 'level': 3 },
          '875': { 'id': 875, 'pid': 871, 'name': '\u6d77\u5b89\u53bf', 'level': 3 },
          '876': { 'id': 876, 'pid': 871, 'name': '\u5982\u4e1c\u53bf', 'level': 3 },
          '877': { 'id': 877, 'pid': 871, 'name': '\u542f\u4e1c\u5e02', 'level': 3 },
          '878': { 'id': 878, 'pid': 871, 'name': '\u5982\u768b\u5e02', 'level': 3 },
          '879': { 'id': 879, 'pid': 871, 'name': '\u6d77\u95e8\u5e02', 'level': 3 }
        }
      },
      '880': {
        'id': 880,
        'pid': 820,
        'name': '\u8fde\u4e91\u6e2f\u5e02',
        'level': 2,
        'region': {
          '881': { 'id': 881, 'pid': 880, 'name': '\u8fde\u4e91\u533a', 'level': 3 },
          '882': { 'id': 882, 'pid': 880, 'name': '\u6d77\u5dde\u533a', 'level': 3 },
          '883': { 'id': 883, 'pid': 880, 'name': '\u8d63\u6986\u533a', 'level': 3 },
          '884': { 'id': 884, 'pid': 880, 'name': '\u4e1c\u6d77\u53bf', 'level': 3 },
          '885': { 'id': 885, 'pid': 880, 'name': '\u704c\u4e91\u53bf', 'level': 3 },
          '886': { 'id': 886, 'pid': 880, 'name': '\u704c\u5357\u53bf', 'level': 3 }
        }
      },
      '887': {
        'id': 887,
        'pid': 820,
        'name': '\u6dee\u5b89\u5e02',
        'level': 2,
        'region': {
          '888': { 'id': 888, 'pid': 887, 'name': '\u6e05\u6cb3\u533a', 'level': 3 },
          '889': { 'id': 889, 'pid': 887, 'name': '\u6dee\u5b89\u533a', 'level': 3 },
          '890': { 'id': 890, 'pid': 887, 'name': '\u6dee\u9634\u533a', 'level': 3 },
          '891': { 'id': 891, 'pid': 887, 'name': '\u6e05\u6d66\u533a', 'level': 3 },
          '892': { 'id': 892, 'pid': 887, 'name': '\u6d9f\u6c34\u53bf', 'level': 3 },
          '893': { 'id': 893, 'pid': 887, 'name': '\u6d2a\u6cfd\u53bf', 'level': 3 },
          '894': { 'id': 894, 'pid': 887, 'name': '\u76f1\u7719\u53bf', 'level': 3 },
          '895': { 'id': 895, 'pid': 887, 'name': '\u91d1\u6e56\u53bf', 'level': 3 }
        }
      },
      '896': {
        'id': 896,
        'pid': 820,
        'name': '\u76d0\u57ce\u5e02',
        'level': 2,
        'region': {
          '897': { 'id': 897, 'pid': 896, 'name': '\u4ead\u6e56\u533a', 'level': 3 },
          '898': { 'id': 898, 'pid': 896, 'name': '\u76d0\u90fd\u533a', 'level': 3 },
          '899': { 'id': 899, 'pid': 896, 'name': '\u54cd\u6c34\u53bf', 'level': 3 },
          '900': { 'id': 900, 'pid': 896, 'name': '\u6ee8\u6d77\u53bf', 'level': 3 },
          '901': { 'id': 901, 'pid': 896, 'name': '\u961c\u5b81\u53bf', 'level': 3 },
          '902': { 'id': 902, 'pid': 896, 'name': '\u5c04\u9633\u53bf', 'level': 3 },
          '903': { 'id': 903, 'pid': 896, 'name': '\u5efa\u6e56\u53bf', 'level': 3 },
          '904': { 'id': 904, 'pid': 896, 'name': '\u4e1c\u53f0\u5e02', 'level': 3 },
          '905': { 'id': 905, 'pid': 896, 'name': '\u5927\u4e30\u5e02', 'level': 3 }
        }
      },
      '906': {
        'id': 906,
        'pid': 820,
        'name': '\u626c\u5dde\u5e02',
        'level': 2,
        'region': {
          '907': { 'id': 907, 'pid': 906, 'name': '\u5e7f\u9675\u533a', 'level': 3 },
          '908': { 'id': 908, 'pid': 906, 'name': '\u9097\u6c5f\u533a', 'level': 3 },
          '909': { 'id': 909, 'pid': 906, 'name': '\u6c5f\u90fd\u533a', 'level': 3 },
          '910': { 'id': 910, 'pid': 906, 'name': '\u5b9d\u5e94\u53bf', 'level': 3 },
          '911': { 'id': 911, 'pid': 906, 'name': '\u4eea\u5f81\u5e02', 'level': 3 },
          '912': { 'id': 912, 'pid': 906, 'name': '\u9ad8\u90ae\u5e02', 'level': 3 }
        }
      },
      '913': {
        'id': 913,
        'pid': 820,
        'name': '\u9547\u6c5f\u5e02',
        'level': 2,
        'region': {
          '914': { 'id': 914, 'pid': 913, 'name': '\u4eac\u53e3\u533a', 'level': 3 },
          '915': { 'id': 915, 'pid': 913, 'name': '\u6da6\u5dde\u533a', 'level': 3 },
          '916': { 'id': 916, 'pid': 913, 'name': '\u4e39\u5f92\u533a', 'level': 3 },
          '917': { 'id': 917, 'pid': 913, 'name': '\u4e39\u9633\u5e02', 'level': 3 },
          '918': { 'id': 918, 'pid': 913, 'name': '\u626c\u4e2d\u5e02', 'level': 3 },
          '919': { 'id': 919, 'pid': 913, 'name': '\u53e5\u5bb9\u5e02', 'level': 3 }
        }
      },
      '920': {
        'id': 920,
        'pid': 820,
        'name': '\u6cf0\u5dde\u5e02',
        'level': 2,
        'region': {
          '921': { 'id': 921, 'pid': 920, 'name': '\u6d77\u9675\u533a', 'level': 3 },
          '922': { 'id': 922, 'pid': 920, 'name': '\u9ad8\u6e2f\u533a', 'level': 3 },
          '923': { 'id': 923, 'pid': 920, 'name': '\u59dc\u5830\u533a', 'level': 3 },
          '924': { 'id': 924, 'pid': 920, 'name': '\u5174\u5316\u5e02', 'level': 3 },
          '925': { 'id': 925, 'pid': 920, 'name': '\u9756\u6c5f\u5e02', 'level': 3 },
          '926': { 'id': 926, 'pid': 920, 'name': '\u6cf0\u5174\u5e02', 'level': 3 }
        }
      },
      '927': {
        'id': 927,
        'pid': 820,
        'name': '\u5bbf\u8fc1\u5e02',
        'level': 2,
        'region': {
          '928': { 'id': 928, 'pid': 927, 'name': '\u5bbf\u57ce\u533a', 'level': 3 },
          '929': { 'id': 929, 'pid': 927, 'name': '\u5bbf\u8c6b\u533a', 'level': 3 },
          '930': { 'id': 930, 'pid': 927, 'name': '\u6cad\u9633\u53bf', 'level': 3 },
          '931': { 'id': 931, 'pid': 927, 'name': '\u6cd7\u9633\u53bf', 'level': 3 },
          '932': { 'id': 932, 'pid': 927, 'name': '\u6cd7\u6d2a\u53bf', 'level': 3 }
        }
      }
    }
  },
  '933': {
    'id': 933, 'pid': 0, 'name': '\u6d59\u6c5f\u7701', 'level': 1, 'city': {
      '934': {
        'id': 934,
        'pid': 933,
        'name': '\u676d\u5dde\u5e02',
        'level': 2,
        'region': {
          '935': { 'id': 935, 'pid': 934, 'name': '\u4e0a\u57ce\u533a', 'level': 3 },
          '936': { 'id': 936, 'pid': 934, 'name': '\u4e0b\u57ce\u533a', 'level': 3 },
          '937': { 'id': 937, 'pid': 934, 'name': '\u6c5f\u5e72\u533a', 'level': 3 },
          '938': { 'id': 938, 'pid': 934, 'name': '\u62f1\u5885\u533a', 'level': 3 },
          '939': { 'id': 939, 'pid': 934, 'name': '\u897f\u6e56\u533a', 'level': 3 },
          '940': { 'id': 940, 'pid': 934, 'name': '\u6ee8\u6c5f\u533a', 'level': 3 },
          '941': { 'id': 941, 'pid': 934, 'name': '\u8427\u5c71\u533a', 'level': 3 },
          '942': { 'id': 942, 'pid': 934, 'name': '\u4f59\u676d\u533a', 'level': 3 },
          '943': { 'id': 943, 'pid': 934, 'name': '\u6850\u5e90\u53bf', 'level': 3 },
          '944': { 'id': 944, 'pid': 934, 'name': '\u6df3\u5b89\u53bf', 'level': 3 },
          '945': { 'id': 945, 'pid': 934, 'name': '\u5efa\u5fb7\u5e02', 'level': 3 },
          '946': { 'id': 946, 'pid': 934, 'name': '\u5bcc\u9633\u533a', 'level': 3 },
          '947': { 'id': 947, 'pid': 934, 'name': '\u4e34\u5b89\u5e02', 'level': 3 }
        }
      },
      '948': {
        'id': 948,
        'pid': 933,
        'name': '\u5b81\u6ce2\u5e02',
        'level': 2,
        'region': {
          '949': { 'id': 949, 'pid': 948, 'name': '\u6d77\u66d9\u533a', 'level': 3 },
          '950': { 'id': 950, 'pid': 948, 'name': '\u6c5f\u4e1c\u533a', 'level': 3 },
          '951': { 'id': 951, 'pid': 948, 'name': '\u6c5f\u5317\u533a', 'level': 3 },
          '952': { 'id': 952, 'pid': 948, 'name': '\u5317\u4ed1\u533a', 'level': 3 },
          '953': { 'id': 953, 'pid': 948, 'name': '\u9547\u6d77\u533a', 'level': 3 },
          '954': { 'id': 954, 'pid': 948, 'name': '\u911e\u5dde\u533a', 'level': 3 },
          '955': { 'id': 955, 'pid': 948, 'name': '\u8c61\u5c71\u53bf', 'level': 3 },
          '956': { 'id': 956, 'pid': 948, 'name': '\u5b81\u6d77\u53bf', 'level': 3 },
          '957': { 'id': 957, 'pid': 948, 'name': '\u4f59\u59da\u5e02', 'level': 3 },
          '958': { 'id': 958, 'pid': 948, 'name': '\u6148\u6eaa\u5e02', 'level': 3 },
          '959': { 'id': 959, 'pid': 948, 'name': '\u5949\u5316\u5e02', 'level': 3 }
        }
      },
      '960': {
        'id': 960,
        'pid': 933,
        'name': '\u6e29\u5dde\u5e02',
        'level': 2,
        'region': {
          '961': { 'id': 961, 'pid': 960, 'name': '\u9e7f\u57ce\u533a', 'level': 3 },
          '962': { 'id': 962, 'pid': 960, 'name': '\u9f99\u6e7e\u533a', 'level': 3 },
          '963': { 'id': 963, 'pid': 960, 'name': '\u74ef\u6d77\u533a', 'level': 3 },
          '964': { 'id': 964, 'pid': 960, 'name': '\u6d1e\u5934\u53bf', 'level': 3 },
          '965': { 'id': 965, 'pid': 960, 'name': '\u6c38\u5609\u53bf', 'level': 3 },
          '966': { 'id': 966, 'pid': 960, 'name': '\u5e73\u9633\u53bf', 'level': 3 },
          '967': { 'id': 967, 'pid': 960, 'name': '\u82cd\u5357\u53bf', 'level': 3 },
          '968': { 'id': 968, 'pid': 960, 'name': '\u6587\u6210\u53bf', 'level': 3 },
          '969': { 'id': 969, 'pid': 960, 'name': '\u6cf0\u987a\u53bf', 'level': 3 },
          '970': { 'id': 970, 'pid': 960, 'name': '\u745e\u5b89\u5e02', 'level': 3 },
          '971': { 'id': 971, 'pid': 960, 'name': '\u4e50\u6e05\u5e02', 'level': 3 }
        }
      },
      '972': {
        'id': 972,
        'pid': 933,
        'name': '\u5609\u5174\u5e02',
        'level': 2,
        'region': {
          '973': { 'id': 973, 'pid': 972, 'name': '\u5357\u6e56\u533a', 'level': 3 },
          '974': { 'id': 974, 'pid': 972, 'name': '\u79c0\u6d32\u533a', 'level': 3 },
          '975': { 'id': 975, 'pid': 972, 'name': '\u5609\u5584\u53bf', 'level': 3 },
          '976': { 'id': 976, 'pid': 972, 'name': '\u6d77\u76d0\u53bf', 'level': 3 },
          '977': { 'id': 977, 'pid': 972, 'name': '\u6d77\u5b81\u5e02', 'level': 3 },
          '978': { 'id': 978, 'pid': 972, 'name': '\u5e73\u6e56\u5e02', 'level': 3 },
          '979': { 'id': 979, 'pid': 972, 'name': '\u6850\u4e61\u5e02', 'level': 3 }
        }
      },
      '980': {
        'id': 980,
        'pid': 933,
        'name': '\u6e56\u5dde\u5e02',
        'level': 2,
        'region': {
          '981': { 'id': 981, 'pid': 980, 'name': '\u5434\u5174\u533a', 'level': 3 },
          '982': { 'id': 982, 'pid': 980, 'name': '\u5357\u6d54\u533a', 'level': 3 },
          '983': { 'id': 983, 'pid': 980, 'name': '\u5fb7\u6e05\u53bf', 'level': 3 },
          '984': { 'id': 984, 'pid': 980, 'name': '\u957f\u5174\u53bf', 'level': 3 },
          '985': { 'id': 985, 'pid': 980, 'name': '\u5b89\u5409\u53bf', 'level': 3 }
        }
      },
      '986': {
        'id': 986,
        'pid': 933,
        'name': '\u7ecd\u5174\u5e02',
        'level': 2,
        'region': {
          '987': { 'id': 987, 'pid': 986, 'name': '\u8d8a\u57ce\u533a', 'level': 3 },
          '988': { 'id': 988, 'pid': 986, 'name': '\u67ef\u6865\u533a', 'level': 3 },
          '989': { 'id': 989, 'pid': 986, 'name': '\u4e0a\u865e\u533a', 'level': 3 },
          '990': { 'id': 990, 'pid': 986, 'name': '\u65b0\u660c\u53bf', 'level': 3 },
          '991': { 'id': 991, 'pid': 986, 'name': '\u8bf8\u66a8\u5e02', 'level': 3 },
          '992': { 'id': 992, 'pid': 986, 'name': '\u5d4a\u5dde\u5e02', 'level': 3 }
        }
      },
      '993': {
        'id': 993,
        'pid': 933,
        'name': '\u91d1\u534e\u5e02',
        'level': 2,
        'region': {
          '994': { 'id': 994, 'pid': 993, 'name': '\u5a7a\u57ce\u533a', 'level': 3 },
          '995': { 'id': 995, 'pid': 993, 'name': '\u91d1\u4e1c\u533a', 'level': 3 },
          '996': { 'id': 996, 'pid': 993, 'name': '\u6b66\u4e49\u53bf', 'level': 3 },
          '997': { 'id': 997, 'pid': 993, 'name': '\u6d66\u6c5f\u53bf', 'level': 3 },
          '998': { 'id': 998, 'pid': 993, 'name': '\u78d0\u5b89\u53bf', 'level': 3 },
          '999': { 'id': 999, 'pid': 993, 'name': '\u5170\u6eaa\u5e02', 'level': 3 },
          '1000': { 'id': 1000, 'pid': 993, 'name': '\u4e49\u4e4c\u5e02', 'level': 3 },
          '1001': { 'id': 1001, 'pid': 993, 'name': '\u4e1c\u9633\u5e02', 'level': 3 },
          '1002': { 'id': 1002, 'pid': 993, 'name': '\u6c38\u5eb7\u5e02', 'level': 3 }
        }
      },
      '1003': {
        'id': 1003,
        'pid': 933,
        'name': '\u8862\u5dde\u5e02',
        'level': 2,
        'region': {
          '1004': { 'id': 1004, 'pid': 1003, 'name': '\u67ef\u57ce\u533a', 'level': 3 },
          '1005': { 'id': 1005, 'pid': 1003, 'name': '\u8862\u6c5f\u533a', 'level': 3 },
          '1006': { 'id': 1006, 'pid': 1003, 'name': '\u5e38\u5c71\u53bf', 'level': 3 },
          '1007': { 'id': 1007, 'pid': 1003, 'name': '\u5f00\u5316\u53bf', 'level': 3 },
          '1008': { 'id': 1008, 'pid': 1003, 'name': '\u9f99\u6e38\u53bf', 'level': 3 },
          '1009': { 'id': 1009, 'pid': 1003, 'name': '\u6c5f\u5c71\u5e02', 'level': 3 }
        }
      },
      '1010': {
        'id': 1010,
        'pid': 933,
        'name': '\u821f\u5c71\u5e02',
        'level': 2,
        'region': {
          '1011': { 'id': 1011, 'pid': 1010, 'name': '\u5b9a\u6d77\u533a', 'level': 3 },
          '1012': { 'id': 1012, 'pid': 1010, 'name': '\u666e\u9640\u533a', 'level': 3 },
          '1013': { 'id': 1013, 'pid': 1010, 'name': '\u5cb1\u5c71\u53bf', 'level': 3 },
          '1014': { 'id': 1014, 'pid': 1010, 'name': '\u5d4a\u6cd7\u53bf', 'level': 3 }
        }
      },
      '1015': {
        'id': 1015,
        'pid': 933,
        'name': '\u53f0\u5dde\u5e02',
        'level': 2,
        'region': {
          '1016': { 'id': 1016, 'pid': 1015, 'name': '\u6912\u6c5f\u533a', 'level': 3 },
          '1017': { 'id': 1017, 'pid': 1015, 'name': '\u9ec4\u5ca9\u533a', 'level': 3 },
          '1018': { 'id': 1018, 'pid': 1015, 'name': '\u8def\u6865\u533a', 'level': 3 },
          '1019': { 'id': 1019, 'pid': 1015, 'name': '\u7389\u73af\u53bf', 'level': 3 },
          '1020': { 'id': 1020, 'pid': 1015, 'name': '\u4e09\u95e8\u53bf', 'level': 3 },
          '1021': { 'id': 1021, 'pid': 1015, 'name': '\u5929\u53f0\u53bf', 'level': 3 },
          '1022': { 'id': 1022, 'pid': 1015, 'name': '\u4ed9\u5c45\u53bf', 'level': 3 },
          '1023': { 'id': 1023, 'pid': 1015, 'name': '\u6e29\u5cad\u5e02', 'level': 3 },
          '1024': { 'id': 1024, 'pid': 1015, 'name': '\u4e34\u6d77\u5e02', 'level': 3 }
        }
      },
      '1025': {
        'id': 1025,
        'pid': 933,
        'name': '\u4e3d\u6c34\u5e02',
        'level': 2,
        'region': {
          '1026': { 'id': 1026, 'pid': 1025, 'name': '\u83b2\u90fd\u533a', 'level': 3 },
          '1027': { 'id': 1027, 'pid': 1025, 'name': '\u9752\u7530\u53bf', 'level': 3 },
          '1028': { 'id': 1028, 'pid': 1025, 'name': '\u7f19\u4e91\u53bf', 'level': 3 },
          '1029': { 'id': 1029, 'pid': 1025, 'name': '\u9042\u660c\u53bf', 'level': 3 },
          '1030': { 'id': 1030, 'pid': 1025, 'name': '\u677e\u9633\u53bf', 'level': 3 },
          '1031': { 'id': 1031, 'pid': 1025, 'name': '\u4e91\u548c\u53bf', 'level': 3 },
          '1032': { 'id': 1032, 'pid': 1025, 'name': '\u5e86\u5143\u53bf', 'level': 3 },
          '1033': { 'id': 1033, 'pid': 1025, 'name': '\u666f\u5b81\u7572\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '1034': { 'id': 1034, 'pid': 1025, 'name': '\u9f99\u6cc9\u5e02', 'level': 3 }
        }
      },
      '1035': {
        'id': 1035,
        'pid': 933,
        'name': '\u821f\u5c71\u7fa4\u5c9b\u65b0\u533a',
        'level': 2,
        'region': {
          '1036': { 'id': 1036, 'pid': 1035, 'name': '\u91d1\u5858\u5c9b', 'level': 3 },
          '1037': { 'id': 1037, 'pid': 1035, 'name': '\u516d\u6a2a\u5c9b', 'level': 3 },
          '1038': { 'id': 1038, 'pid': 1035, 'name': '\u8862\u5c71\u5c9b', 'level': 3 },
          '1039': { 'id': 1039, 'pid': 1035, 'name': '\u821f\u5c71\u672c\u5c9b\u897f\u5317\u90e8', 'level': 3 },
          '1040': { 'id': 1040, 'pid': 1035, 'name': '\u5cb1\u5c71\u5c9b\u897f\u5357\u90e8', 'level': 3 },
          '1041': { 'id': 1041, 'pid': 1035, 'name': '\u6cd7\u7901\u5c9b', 'level': 3 },
          '1042': { 'id': 1042, 'pid': 1035, 'name': '\u6731\u5bb6\u5c16\u5c9b', 'level': 3 },
          '1043': { 'id': 1043, 'pid': 1035, 'name': '\u6d0b\u5c71\u5c9b', 'level': 3 },
          '1044': { 'id': 1044, 'pid': 1035, 'name': '\u957f\u6d82\u5c9b', 'level': 3 },
          '1045': { 'id': 1045, 'pid': 1035, 'name': '\u867e\u5cd9\u5c9b', 'level': 3 }
        }
      }
    }
  },
  '1046': {
    'id': 1046, 'pid': 0, 'name': '\u5b89\u5fbd\u7701', 'level': 1, 'city': {
      '1047': {
        'id': 1047,
        'pid': 1046,
        'name': '\u5408\u80a5\u5e02',
        'level': 2,
        'region': {
          '1048': { 'id': 1048, 'pid': 1047, 'name': '\u7476\u6d77\u533a', 'level': 3 },
          '1049': { 'id': 1049, 'pid': 1047, 'name': '\u5e90\u9633\u533a', 'level': 3 },
          '1050': { 'id': 1050, 'pid': 1047, 'name': '\u8700\u5c71\u533a', 'level': 3 },
          '1051': { 'id': 1051, 'pid': 1047, 'name': '\u5305\u6cb3\u533a', 'level': 3 },
          '1052': { 'id': 1052, 'pid': 1047, 'name': '\u957f\u4e30\u53bf', 'level': 3 },
          '1053': { 'id': 1053, 'pid': 1047, 'name': '\u80a5\u4e1c\u53bf', 'level': 3 },
          '1054': { 'id': 1054, 'pid': 1047, 'name': '\u80a5\u897f\u53bf', 'level': 3 },
          '1055': { 'id': 1055, 'pid': 1047, 'name': '\u5e90\u6c5f\u53bf', 'level': 3 },
          '1056': { 'id': 1056, 'pid': 1047, 'name': '\u5de2\u6e56\u5e02', 'level': 3 }
        }
      },
      '1057': {
        'id': 1057,
        'pid': 1046,
        'name': '\u829c\u6e56\u5e02',
        'level': 2,
        'region': {
          '1058': { 'id': 1058, 'pid': 1057, 'name': '\u955c\u6e56\u533a', 'level': 3 },
          '1059': { 'id': 1059, 'pid': 1057, 'name': '\u5f0b\u6c5f\u533a', 'level': 3 },
          '1060': { 'id': 1060, 'pid': 1057, 'name': '\u9e20\u6c5f\u533a', 'level': 3 },
          '1061': { 'id': 1061, 'pid': 1057, 'name': '\u4e09\u5c71\u533a', 'level': 3 },
          '1062': { 'id': 1062, 'pid': 1057, 'name': '\u829c\u6e56\u53bf', 'level': 3 },
          '1063': { 'id': 1063, 'pid': 1057, 'name': '\u7e41\u660c\u53bf', 'level': 3 },
          '1064': { 'id': 1064, 'pid': 1057, 'name': '\u5357\u9675\u53bf', 'level': 3 },
          '1065': { 'id': 1065, 'pid': 1057, 'name': '\u65e0\u4e3a\u53bf', 'level': 3 }
        }
      },
      '1066': {
        'id': 1066,
        'pid': 1046,
        'name': '\u868c\u57e0\u5e02',
        'level': 2,
        'region': {
          '1067': { 'id': 1067, 'pid': 1066, 'name': '\u9f99\u5b50\u6e56\u533a', 'level': 3 },
          '1068': { 'id': 1068, 'pid': 1066, 'name': '\u868c\u5c71\u533a', 'level': 3 },
          '1069': { 'id': 1069, 'pid': 1066, 'name': '\u79b9\u4f1a\u533a', 'level': 3 },
          '1070': { 'id': 1070, 'pid': 1066, 'name': '\u6dee\u4e0a\u533a', 'level': 3 },
          '1071': { 'id': 1071, 'pid': 1066, 'name': '\u6000\u8fdc\u53bf', 'level': 3 },
          '1072': { 'id': 1072, 'pid': 1066, 'name': '\u4e94\u6cb3\u53bf', 'level': 3 },
          '1073': { 'id': 1073, 'pid': 1066, 'name': '\u56fa\u9547\u53bf', 'level': 3 }
        }
      },
      '1074': {
        'id': 1074,
        'pid': 1046,
        'name': '\u6dee\u5357\u5e02',
        'level': 2,
        'region': {
          '1075': { 'id': 1075, 'pid': 1074, 'name': '\u5927\u901a\u533a', 'level': 3 },
          '1076': { 'id': 1076, 'pid': 1074, 'name': '\u7530\u5bb6\u5eb5\u533a', 'level': 3 },
          '1077': { 'id': 1077, 'pid': 1074, 'name': '\u8c22\u5bb6\u96c6\u533a', 'level': 3 },
          '1078': { 'id': 1078, 'pid': 1074, 'name': '\u516b\u516c\u5c71\u533a', 'level': 3 },
          '1079': { 'id': 1079, 'pid': 1074, 'name': '\u6f58\u96c6\u533a', 'level': 3 },
          '1080': { 'id': 1080, 'pid': 1074, 'name': '\u51e4\u53f0\u53bf', 'level': 3 }
        }
      },
      '1081': {
        'id': 1081,
        'pid': 1046,
        'name': '\u9a6c\u978d\u5c71\u5e02',
        'level': 2,
        'region': {
          '1082': { 'id': 1082, 'pid': 1081, 'name': '\u82b1\u5c71\u533a', 'level': 3 },
          '1083': { 'id': 1083, 'pid': 1081, 'name': '\u96e8\u5c71\u533a', 'level': 3 },
          '1084': { 'id': 1084, 'pid': 1081, 'name': '\u535a\u671b\u533a', 'level': 3 },
          '1085': { 'id': 1085, 'pid': 1081, 'name': '\u5f53\u6d82\u53bf', 'level': 3 },
          '1086': { 'id': 1086, 'pid': 1081, 'name': '\u542b\u5c71\u53bf', 'level': 3 },
          '1087': { 'id': 1087, 'pid': 1081, 'name': '\u548c\u53bf', 'level': 3 }
        }
      },
      '1088': {
        'id': 1088,
        'pid': 1046,
        'name': '\u6dee\u5317\u5e02',
        'level': 2,
        'region': {
          '1089': { 'id': 1089, 'pid': 1088, 'name': '\u675c\u96c6\u533a', 'level': 3 },
          '1090': { 'id': 1090, 'pid': 1088, 'name': '\u76f8\u5c71\u533a', 'level': 3 },
          '1091': { 'id': 1091, 'pid': 1088, 'name': '\u70c8\u5c71\u533a', 'level': 3 },
          '1092': { 'id': 1092, 'pid': 1088, 'name': '\u6fc9\u6eaa\u53bf', 'level': 3 }
        }
      },
      '1093': {
        'id': 1093,
        'pid': 1046,
        'name': '\u94dc\u9675\u5e02',
        'level': 2,
        'region': {
          '1094': { 'id': 1094, 'pid': 1093, 'name': '\u94dc\u5b98\u5c71\u533a', 'level': 3 },
          '1095': { 'id': 1095, 'pid': 1093, 'name': '\u72ee\u5b50\u5c71\u533a', 'level': 3 },
          '1096': { 'id': 1096, 'pid': 1093, 'name': '\u90ca\u533a', 'level': 3 },
          '1097': { 'id': 1097, 'pid': 1093, 'name': '\u94dc\u9675\u53bf', 'level': 3 }
        }
      },
      '1098': {
        'id': 1098,
        'pid': 1046,
        'name': '\u5b89\u5e86\u5e02',
        'level': 2,
        'region': {
          '1099': { 'id': 1099, 'pid': 1098, 'name': '\u8fce\u6c5f\u533a', 'level': 3 },
          '1100': { 'id': 1100, 'pid': 1098, 'name': '\u5927\u89c2\u533a', 'level': 3 },
          '1101': { 'id': 1101, 'pid': 1098, 'name': '\u5b9c\u79c0\u533a', 'level': 3 },
          '1102': { 'id': 1102, 'pid': 1098, 'name': '\u6000\u5b81\u53bf', 'level': 3 },
          '1103': { 'id': 1103, 'pid': 1098, 'name': '\u679e\u9633\u53bf', 'level': 3 },
          '1104': { 'id': 1104, 'pid': 1098, 'name': '\u6f5c\u5c71\u53bf', 'level': 3 },
          '1105': { 'id': 1105, 'pid': 1098, 'name': '\u592a\u6e56\u53bf', 'level': 3 },
          '1106': { 'id': 1106, 'pid': 1098, 'name': '\u5bbf\u677e\u53bf', 'level': 3 },
          '1107': { 'id': 1107, 'pid': 1098, 'name': '\u671b\u6c5f\u53bf', 'level': 3 },
          '1108': { 'id': 1108, 'pid': 1098, 'name': '\u5cb3\u897f\u53bf', 'level': 3 },
          '1109': { 'id': 1109, 'pid': 1098, 'name': '\u6850\u57ce\u5e02', 'level': 3 }
        }
      },
      '1110': {
        'id': 1110,
        'pid': 1046,
        'name': '\u9ec4\u5c71\u5e02',
        'level': 2,
        'region': {
          '1111': { 'id': 1111, 'pid': 1110, 'name': '\u5c6f\u6eaa\u533a', 'level': 3 },
          '1112': { 'id': 1112, 'pid': 1110, 'name': '\u9ec4\u5c71\u533a', 'level': 3 },
          '1113': { 'id': 1113, 'pid': 1110, 'name': '\u5fbd\u5dde\u533a', 'level': 3 },
          '1114': { 'id': 1114, 'pid': 1110, 'name': '\u6b59\u53bf', 'level': 3 },
          '1115': { 'id': 1115, 'pid': 1110, 'name': '\u4f11\u5b81\u53bf', 'level': 3 },
          '1116': { 'id': 1116, 'pid': 1110, 'name': '\u9edf\u53bf', 'level': 3 },
          '1117': { 'id': 1117, 'pid': 1110, 'name': '\u7941\u95e8\u53bf', 'level': 3 }
        }
      },
      '1118': {
        'id': 1118,
        'pid': 1046,
        'name': '\u6ec1\u5dde\u5e02',
        'level': 2,
        'region': {
          '1119': { 'id': 1119, 'pid': 1118, 'name': '\u7405\u740a\u533a', 'level': 3 },
          '1120': { 'id': 1120, 'pid': 1118, 'name': '\u5357\u8c2f\u533a', 'level': 3 },
          '1121': { 'id': 1121, 'pid': 1118, 'name': '\u6765\u5b89\u53bf', 'level': 3 },
          '1122': { 'id': 1122, 'pid': 1118, 'name': '\u5168\u6912\u53bf', 'level': 3 },
          '1123': { 'id': 1123, 'pid': 1118, 'name': '\u5b9a\u8fdc\u53bf', 'level': 3 },
          '1124': { 'id': 1124, 'pid': 1118, 'name': '\u51e4\u9633\u53bf', 'level': 3 },
          '1125': { 'id': 1125, 'pid': 1118, 'name': '\u5929\u957f\u5e02', 'level': 3 },
          '1126': { 'id': 1126, 'pid': 1118, 'name': '\u660e\u5149\u5e02', 'level': 3 }
        }
      },
      '1127': {
        'id': 1127,
        'pid': 1046,
        'name': '\u961c\u9633\u5e02',
        'level': 2,
        'region': {
          '1128': { 'id': 1128, 'pid': 1127, 'name': '\u988d\u5dde\u533a', 'level': 3 },
          '1129': { 'id': 1129, 'pid': 1127, 'name': '\u988d\u4e1c\u533a', 'level': 3 },
          '1130': { 'id': 1130, 'pid': 1127, 'name': '\u988d\u6cc9\u533a', 'level': 3 },
          '1131': { 'id': 1131, 'pid': 1127, 'name': '\u4e34\u6cc9\u53bf', 'level': 3 },
          '1132': { 'id': 1132, 'pid': 1127, 'name': '\u592a\u548c\u53bf', 'level': 3 },
          '1133': { 'id': 1133, 'pid': 1127, 'name': '\u961c\u5357\u53bf', 'level': 3 },
          '1134': { 'id': 1134, 'pid': 1127, 'name': '\u988d\u4e0a\u53bf', 'level': 3 },
          '1135': { 'id': 1135, 'pid': 1127, 'name': '\u754c\u9996\u5e02', 'level': 3 }
        }
      },
      '1136': {
        'id': 1136,
        'pid': 1046,
        'name': '\u5bbf\u5dde\u5e02',
        'level': 2,
        'region': {
          '1137': { 'id': 1137, 'pid': 1136, 'name': '\u57c7\u6865\u533a', 'level': 3 },
          '1138': { 'id': 1138, 'pid': 1136, 'name': '\u7800\u5c71\u53bf', 'level': 3 },
          '1139': { 'id': 1139, 'pid': 1136, 'name': '\u8427\u53bf', 'level': 3 },
          '1140': { 'id': 1140, 'pid': 1136, 'name': '\u7075\u74a7\u53bf', 'level': 3 },
          '1141': { 'id': 1141, 'pid': 1136, 'name': '\u6cd7\u53bf', 'level': 3 }
        }
      },
      '1142': {
        'id': 1142,
        'pid': 1046,
        'name': '\u516d\u5b89\u5e02',
        'level': 2,
        'region': {
          '1143': { 'id': 1143, 'pid': 1142, 'name': '\u91d1\u5b89\u533a', 'level': 3 },
          '1144': { 'id': 1144, 'pid': 1142, 'name': '\u88d5\u5b89\u533a', 'level': 3 },
          '1145': { 'id': 1145, 'pid': 1142, 'name': '\u5bff\u53bf', 'level': 3 },
          '1146': { 'id': 1146, 'pid': 1142, 'name': '\u970d\u90b1\u53bf', 'level': 3 },
          '1147': { 'id': 1147, 'pid': 1142, 'name': '\u8212\u57ce\u53bf', 'level': 3 },
          '1148': { 'id': 1148, 'pid': 1142, 'name': '\u91d1\u5be8\u53bf', 'level': 3 },
          '1149': { 'id': 1149, 'pid': 1142, 'name': '\u970d\u5c71\u53bf', 'level': 3 }
        }
      },
      '1150': {
        'id': 1150,
        'pid': 1046,
        'name': '\u4eb3\u5dde\u5e02',
        'level': 2,
        'region': {
          '1151': { 'id': 1151, 'pid': 1150, 'name': '\u8c2f\u57ce\u533a', 'level': 3 },
          '1152': { 'id': 1152, 'pid': 1150, 'name': '\u6da1\u9633\u53bf', 'level': 3 },
          '1153': { 'id': 1153, 'pid': 1150, 'name': '\u8499\u57ce\u53bf', 'level': 3 },
          '1154': { 'id': 1154, 'pid': 1150, 'name': '\u5229\u8f9b\u53bf', 'level': 3 }
        }
      },
      '1155': {
        'id': 1155,
        'pid': 1046,
        'name': '\u6c60\u5dde\u5e02',
        'level': 2,
        'region': {
          '1156': { 'id': 1156, 'pid': 1155, 'name': '\u8d35\u6c60\u533a', 'level': 3 },
          '1157': { 'id': 1157, 'pid': 1155, 'name': '\u4e1c\u81f3\u53bf', 'level': 3 },
          '1158': { 'id': 1158, 'pid': 1155, 'name': '\u77f3\u53f0\u53bf', 'level': 3 },
          '1159': { 'id': 1159, 'pid': 1155, 'name': '\u9752\u9633\u53bf', 'level': 3 }
        }
      },
      '1160': {
        'id': 1160,
        'pid': 1046,
        'name': '\u5ba3\u57ce\u5e02',
        'level': 2,
        'region': {
          '1161': { 'id': 1161, 'pid': 1160, 'name': '\u5ba3\u5dde\u533a', 'level': 3 },
          '1162': { 'id': 1162, 'pid': 1160, 'name': '\u90ce\u6eaa\u53bf', 'level': 3 },
          '1163': { 'id': 1163, 'pid': 1160, 'name': '\u5e7f\u5fb7\u53bf', 'level': 3 },
          '1164': { 'id': 1164, 'pid': 1160, 'name': '\u6cfe\u53bf', 'level': 3 },
          '1165': { 'id': 1165, 'pid': 1160, 'name': '\u7ee9\u6eaa\u53bf', 'level': 3 },
          '1166': { 'id': 1166, 'pid': 1160, 'name': '\u65cc\u5fb7\u53bf', 'level': 3 },
          '1167': { 'id': 1167, 'pid': 1160, 'name': '\u5b81\u56fd\u5e02', 'level': 3 }
        }
      }
    }
  },
  '1168': {
    'id': 1168, 'pid': 0, 'name': '\u798f\u5efa\u7701', 'level': 1, 'city': {
      '1169': {
        'id': 1169,
        'pid': 1168,
        'name': '\u798f\u5dde\u5e02',
        'level': 2,
        'region': {
          '1170': { 'id': 1170, 'pid': 1169, 'name': '\u9f13\u697c\u533a', 'level': 3 },
          '1171': { 'id': 1171, 'pid': 1169, 'name': '\u53f0\u6c5f\u533a', 'level': 3 },
          '1172': { 'id': 1172, 'pid': 1169, 'name': '\u4ed3\u5c71\u533a', 'level': 3 },
          '1173': { 'id': 1173, 'pid': 1169, 'name': '\u9a6c\u5c3e\u533a', 'level': 3 },
          '1174': { 'id': 1174, 'pid': 1169, 'name': '\u664b\u5b89\u533a', 'level': 3 },
          '1175': { 'id': 1175, 'pid': 1169, 'name': '\u95fd\u4faf\u53bf', 'level': 3 },
          '1176': { 'id': 1176, 'pid': 1169, 'name': '\u8fde\u6c5f\u53bf', 'level': 3 },
          '1177': { 'id': 1177, 'pid': 1169, 'name': '\u7f57\u6e90\u53bf', 'level': 3 },
          '1178': { 'id': 1178, 'pid': 1169, 'name': '\u95fd\u6e05\u53bf', 'level': 3 },
          '1179': { 'id': 1179, 'pid': 1169, 'name': '\u6c38\u6cf0\u53bf', 'level': 3 },
          '1180': { 'id': 1180, 'pid': 1169, 'name': '\u5e73\u6f6d\u53bf', 'level': 3 },
          '1181': { 'id': 1181, 'pid': 1169, 'name': '\u798f\u6e05\u5e02', 'level': 3 },
          '1182': { 'id': 1182, 'pid': 1169, 'name': '\u957f\u4e50\u5e02', 'level': 3 }
        }
      },
      '1183': {
        'id': 1183,
        'pid': 1168,
        'name': '\u53a6\u95e8\u5e02',
        'level': 2,
        'region': {
          '1184': { 'id': 1184, 'pid': 1183, 'name': '\u601d\u660e\u533a', 'level': 3 },
          '1185': { 'id': 1185, 'pid': 1183, 'name': '\u6d77\u6ca7\u533a', 'level': 3 },
          '1186': { 'id': 1186, 'pid': 1183, 'name': '\u6e56\u91cc\u533a', 'level': 3 },
          '1187': { 'id': 1187, 'pid': 1183, 'name': '\u96c6\u7f8e\u533a', 'level': 3 },
          '1188': { 'id': 1188, 'pid': 1183, 'name': '\u540c\u5b89\u533a', 'level': 3 },
          '1189': { 'id': 1189, 'pid': 1183, 'name': '\u7fd4\u5b89\u533a', 'level': 3 }
        }
      },
      '1190': {
        'id': 1190,
        'pid': 1168,
        'name': '\u8386\u7530\u5e02',
        'level': 2,
        'region': {
          '1191': { 'id': 1191, 'pid': 1190, 'name': '\u57ce\u53a2\u533a', 'level': 3 },
          '1192': { 'id': 1192, 'pid': 1190, 'name': '\u6db5\u6c5f\u533a', 'level': 3 },
          '1193': { 'id': 1193, 'pid': 1190, 'name': '\u8354\u57ce\u533a', 'level': 3 },
          '1194': { 'id': 1194, 'pid': 1190, 'name': '\u79c0\u5c7f\u533a', 'level': 3 },
          '1195': { 'id': 1195, 'pid': 1190, 'name': '\u4ed9\u6e38\u53bf', 'level': 3 }
        }
      },
      '1196': {
        'id': 1196,
        'pid': 1168,
        'name': '\u4e09\u660e\u5e02',
        'level': 2,
        'region': {
          '1197': { 'id': 1197, 'pid': 1196, 'name': '\u6885\u5217\u533a', 'level': 3 },
          '1198': { 'id': 1198, 'pid': 1196, 'name': '\u4e09\u5143\u533a', 'level': 3 },
          '1199': { 'id': 1199, 'pid': 1196, 'name': '\u660e\u6eaa\u53bf', 'level': 3 },
          '1200': { 'id': 1200, 'pid': 1196, 'name': '\u6e05\u6d41\u53bf', 'level': 3 },
          '1201': { 'id': 1201, 'pid': 1196, 'name': '\u5b81\u5316\u53bf', 'level': 3 },
          '1202': { 'id': 1202, 'pid': 1196, 'name': '\u5927\u7530\u53bf', 'level': 3 },
          '1203': { 'id': 1203, 'pid': 1196, 'name': '\u5c24\u6eaa\u53bf', 'level': 3 },
          '1204': { 'id': 1204, 'pid': 1196, 'name': '\u6c99\u53bf', 'level': 3 },
          '1205': { 'id': 1205, 'pid': 1196, 'name': '\u5c06\u4e50\u53bf', 'level': 3 },
          '1206': { 'id': 1206, 'pid': 1196, 'name': '\u6cf0\u5b81\u53bf', 'level': 3 },
          '1207': { 'id': 1207, 'pid': 1196, 'name': '\u5efa\u5b81\u53bf', 'level': 3 },
          '1208': { 'id': 1208, 'pid': 1196, 'name': '\u6c38\u5b89\u5e02', 'level': 3 }
        }
      },
      '1209': {
        'id': 1209,
        'pid': 1168,
        'name': '\u6cc9\u5dde\u5e02',
        'level': 2,
        'region': {
          '1210': { 'id': 1210, 'pid': 1209, 'name': '\u9ca4\u57ce\u533a', 'level': 3 },
          '1211': { 'id': 1211, 'pid': 1209, 'name': '\u4e30\u6cfd\u533a', 'level': 3 },
          '1212': { 'id': 1212, 'pid': 1209, 'name': '\u6d1b\u6c5f\u533a', 'level': 3 },
          '1213': { 'id': 1213, 'pid': 1209, 'name': '\u6cc9\u6e2f\u533a', 'level': 3 },
          '1214': { 'id': 1214, 'pid': 1209, 'name': '\u60e0\u5b89\u53bf', 'level': 3 },
          '1215': { 'id': 1215, 'pid': 1209, 'name': '\u5b89\u6eaa\u53bf', 'level': 3 },
          '1216': { 'id': 1216, 'pid': 1209, 'name': '\u6c38\u6625\u53bf', 'level': 3 },
          '1217': { 'id': 1217, 'pid': 1209, 'name': '\u5fb7\u5316\u53bf', 'level': 3 },
          '1218': { 'id': 1218, 'pid': 1209, 'name': '\u91d1\u95e8\u53bf', 'level': 3 },
          '1219': { 'id': 1219, 'pid': 1209, 'name': '\u77f3\u72ee\u5e02', 'level': 3 },
          '1220': { 'id': 1220, 'pid': 1209, 'name': '\u664b\u6c5f\u5e02', 'level': 3 },
          '1221': { 'id': 1221, 'pid': 1209, 'name': '\u5357\u5b89\u5e02', 'level': 3 }
        }
      },
      '1222': {
        'id': 1222,
        'pid': 1168,
        'name': '\u6f33\u5dde\u5e02',
        'level': 2,
        'region': {
          '1223': { 'id': 1223, 'pid': 1222, 'name': '\u8297\u57ce\u533a', 'level': 3 },
          '1224': { 'id': 1224, 'pid': 1222, 'name': '\u9f99\u6587\u533a', 'level': 3 },
          '1225': { 'id': 1225, 'pid': 1222, 'name': '\u4e91\u9704\u53bf', 'level': 3 },
          '1226': { 'id': 1226, 'pid': 1222, 'name': '\u6f33\u6d66\u53bf', 'level': 3 },
          '1227': { 'id': 1227, 'pid': 1222, 'name': '\u8bcf\u5b89\u53bf', 'level': 3 },
          '1228': { 'id': 1228, 'pid': 1222, 'name': '\u957f\u6cf0\u53bf', 'level': 3 },
          '1229': { 'id': 1229, 'pid': 1222, 'name': '\u4e1c\u5c71\u53bf', 'level': 3 },
          '1230': { 'id': 1230, 'pid': 1222, 'name': '\u5357\u9756\u53bf', 'level': 3 },
          '1231': { 'id': 1231, 'pid': 1222, 'name': '\u5e73\u548c\u53bf', 'level': 3 },
          '1232': { 'id': 1232, 'pid': 1222, 'name': '\u534e\u5b89\u53bf', 'level': 3 },
          '1233': { 'id': 1233, 'pid': 1222, 'name': '\u9f99\u6d77\u5e02', 'level': 3 }
        }
      },
      '1234': {
        'id': 1234,
        'pid': 1168,
        'name': '\u5357\u5e73\u5e02',
        'level': 2,
        'region': {
          '1235': { 'id': 1235, 'pid': 1234, 'name': '\u5ef6\u5e73\u533a', 'level': 3 },
          '1236': { 'id': 1236, 'pid': 1234, 'name': '\u5efa\u9633\u533a', 'level': 3 },
          '1237': { 'id': 1237, 'pid': 1234, 'name': '\u987a\u660c\u53bf', 'level': 3 },
          '1238': { 'id': 1238, 'pid': 1234, 'name': '\u6d66\u57ce\u53bf', 'level': 3 },
          '1239': { 'id': 1239, 'pid': 1234, 'name': '\u5149\u6cfd\u53bf', 'level': 3 },
          '1240': { 'id': 1240, 'pid': 1234, 'name': '\u677e\u6eaa\u53bf', 'level': 3 },
          '1241': { 'id': 1241, 'pid': 1234, 'name': '\u653f\u548c\u53bf', 'level': 3 },
          '1242': { 'id': 1242, 'pid': 1234, 'name': '\u90b5\u6b66\u5e02', 'level': 3 },
          '1243': { 'id': 1243, 'pid': 1234, 'name': '\u6b66\u5937\u5c71\u5e02', 'level': 3 },
          '1244': { 'id': 1244, 'pid': 1234, 'name': '\u5efa\u74ef\u5e02', 'level': 3 }
        }
      },
      '1245': {
        'id': 1245,
        'pid': 1168,
        'name': '\u9f99\u5ca9\u5e02',
        'level': 2,
        'region': {
          '1246': { 'id': 1246, 'pid': 1245, 'name': '\u65b0\u7f57\u533a', 'level': 3 },
          '1247': { 'id': 1247, 'pid': 1245, 'name': '\u957f\u6c40\u53bf', 'level': 3 },
          '1248': { 'id': 1248, 'pid': 1245, 'name': '\u6c38\u5b9a\u533a', 'level': 3 },
          '1249': { 'id': 1249, 'pid': 1245, 'name': '\u4e0a\u676d\u53bf', 'level': 3 },
          '1250': { 'id': 1250, 'pid': 1245, 'name': '\u6b66\u5e73\u53bf', 'level': 3 },
          '1251': { 'id': 1251, 'pid': 1245, 'name': '\u8fde\u57ce\u53bf', 'level': 3 },
          '1252': { 'id': 1252, 'pid': 1245, 'name': '\u6f33\u5e73\u5e02', 'level': 3 }
        }
      },
      '1253': {
        'id': 1253,
        'pid': 1168,
        'name': '\u5b81\u5fb7\u5e02',
        'level': 2,
        'region': {
          '1254': { 'id': 1254, 'pid': 1253, 'name': '\u8549\u57ce\u533a', 'level': 3 },
          '1255': { 'id': 1255, 'pid': 1253, 'name': '\u971e\u6d66\u53bf', 'level': 3 },
          '1256': { 'id': 1256, 'pid': 1253, 'name': '\u53e4\u7530\u53bf', 'level': 3 },
          '1257': { 'id': 1257, 'pid': 1253, 'name': '\u5c4f\u5357\u53bf', 'level': 3 },
          '1258': { 'id': 1258, 'pid': 1253, 'name': '\u5bff\u5b81\u53bf', 'level': 3 },
          '1259': { 'id': 1259, 'pid': 1253, 'name': '\u5468\u5b81\u53bf', 'level': 3 },
          '1260': { 'id': 1260, 'pid': 1253, 'name': '\u67d8\u8363\u53bf', 'level': 3 },
          '1261': { 'id': 1261, 'pid': 1253, 'name': '\u798f\u5b89\u5e02', 'level': 3 },
          '1262': { 'id': 1262, 'pid': 1253, 'name': '\u798f\u9f0e\u5e02', 'level': 3 }
        }
      }
    }
  },
  '1263': {
    'id': 1263, 'pid': 0, 'name': '\u6c5f\u897f\u7701', 'level': 1, 'city': {
      '1264': {
        'id': 1264,
        'pid': 1263,
        'name': '\u5357\u660c\u5e02',
        'level': 2,
        'region': {
          '1265': { 'id': 1265, 'pid': 1264, 'name': '\u4e1c\u6e56\u533a', 'level': 3 },
          '1266': { 'id': 1266, 'pid': 1264, 'name': '\u897f\u6e56\u533a', 'level': 3 },
          '1267': { 'id': 1267, 'pid': 1264, 'name': '\u9752\u4e91\u8c31\u533a', 'level': 3 },
          '1268': { 'id': 1268, 'pid': 1264, 'name': '\u6e7e\u91cc\u533a', 'level': 3 },
          '1269': { 'id': 1269, 'pid': 1264, 'name': '\u9752\u5c71\u6e56\u533a', 'level': 3 },
          '1270': { 'id': 1270, 'pid': 1264, 'name': '\u5357\u660c\u53bf', 'level': 3 },
          '1271': { 'id': 1271, 'pid': 1264, 'name': '\u65b0\u5efa\u53bf', 'level': 3 },
          '1272': { 'id': 1272, 'pid': 1264, 'name': '\u5b89\u4e49\u53bf', 'level': 3 },
          '1273': { 'id': 1273, 'pid': 1264, 'name': '\u8fdb\u8d24\u53bf', 'level': 3 }
        }
      },
      '1274': {
        'id': 1274,
        'pid': 1263,
        'name': '\u666f\u5fb7\u9547\u5e02',
        'level': 2,
        'region': {
          '1275': { 'id': 1275, 'pid': 1274, 'name': '\u660c\u6c5f\u533a', 'level': 3 },
          '1276': { 'id': 1276, 'pid': 1274, 'name': '\u73e0\u5c71\u533a', 'level': 3 },
          '1277': { 'id': 1277, 'pid': 1274, 'name': '\u6d6e\u6881\u53bf', 'level': 3 },
          '1278': { 'id': 1278, 'pid': 1274, 'name': '\u4e50\u5e73\u5e02', 'level': 3 }
        }
      },
      '1279': {
        'id': 1279,
        'pid': 1263,
        'name': '\u840d\u4e61\u5e02',
        'level': 2,
        'region': {
          '1280': { 'id': 1280, 'pid': 1279, 'name': '\u5b89\u6e90\u533a', 'level': 3 },
          '1281': { 'id': 1281, 'pid': 1279, 'name': '\u6e58\u4e1c\u533a', 'level': 3 },
          '1282': { 'id': 1282, 'pid': 1279, 'name': '\u83b2\u82b1\u53bf', 'level': 3 },
          '1283': { 'id': 1283, 'pid': 1279, 'name': '\u4e0a\u6817\u53bf', 'level': 3 },
          '1284': { 'id': 1284, 'pid': 1279, 'name': '\u82a6\u6eaa\u53bf', 'level': 3 }
        }
      },
      '1285': {
        'id': 1285,
        'pid': 1263,
        'name': '\u4e5d\u6c5f\u5e02',
        'level': 2,
        'region': {
          '1286': { 'id': 1286, 'pid': 1285, 'name': '\u5e90\u5c71\u533a', 'level': 3 },
          '1287': { 'id': 1287, 'pid': 1285, 'name': '\u6d54\u9633\u533a', 'level': 3 },
          '1288': { 'id': 1288, 'pid': 1285, 'name': '\u4e5d\u6c5f\u53bf', 'level': 3 },
          '1289': { 'id': 1289, 'pid': 1285, 'name': '\u6b66\u5b81\u53bf', 'level': 3 },
          '1290': { 'id': 1290, 'pid': 1285, 'name': '\u4fee\u6c34\u53bf', 'level': 3 },
          '1291': { 'id': 1291, 'pid': 1285, 'name': '\u6c38\u4fee\u53bf', 'level': 3 },
          '1292': { 'id': 1292, 'pid': 1285, 'name': '\u5fb7\u5b89\u53bf', 'level': 3 },
          '1293': { 'id': 1293, 'pid': 1285, 'name': '\u661f\u5b50\u53bf', 'level': 3 },
          '1294': { 'id': 1294, 'pid': 1285, 'name': '\u90fd\u660c\u53bf', 'level': 3 },
          '1295': { 'id': 1295, 'pid': 1285, 'name': '\u6e56\u53e3\u53bf', 'level': 3 },
          '1296': { 'id': 1296, 'pid': 1285, 'name': '\u5f6d\u6cfd\u53bf', 'level': 3 },
          '1297': { 'id': 1297, 'pid': 1285, 'name': '\u745e\u660c\u5e02', 'level': 3 },
          '1298': { 'id': 1298, 'pid': 1285, 'name': '\u5171\u9752\u57ce\u5e02', 'level': 3 }
        }
      },
      '1299': {
        'id': 1299,
        'pid': 1263,
        'name': '\u65b0\u4f59\u5e02',
        'level': 2,
        'region': {
          '1300': { 'id': 1300, 'pid': 1299, 'name': '\u6e1d\u6c34\u533a', 'level': 3 },
          '1301': { 'id': 1301, 'pid': 1299, 'name': '\u5206\u5b9c\u53bf', 'level': 3 }
        }
      },
      '1302': {
        'id': 1302,
        'pid': 1263,
        'name': '\u9e70\u6f6d\u5e02',
        'level': 2,
        'region': {
          '1303': { 'id': 1303, 'pid': 1302, 'name': '\u6708\u6e56\u533a', 'level': 3 },
          '1304': { 'id': 1304, 'pid': 1302, 'name': '\u4f59\u6c5f\u53bf', 'level': 3 },
          '1305': { 'id': 1305, 'pid': 1302, 'name': '\u8d35\u6eaa\u5e02', 'level': 3 }
        }
      },
      '1306': {
        'id': 1306, 'pid': 1263, 'name': '\u8d63\u5dde\u5e02', 'level': 2, 'region': {
          '1307': { 'id': 1307, 'pid': 1306, 'name': '\u7ae0\u8d21\u533a', 'level': 3 },
          '1308': { 'id': 1308, 'pid': 1306, 'name': '\u5357\u5eb7\u533a', 'level': 3 },
          '1309': { 'id': 1309, 'pid': 1306, 'name': '\u8d63\u53bf', 'level': 3 },
          '1310': { 'id': 1310, 'pid': 1306, 'name': '\u4fe1\u4e30\u53bf', 'level': 3 },
          '1311': { 'id': 1311, 'pid': 1306, 'name': '\u5927\u4f59\u53bf', 'level': 3 },
          '1312': { 'id': 1312, 'pid': 1306, 'name': '\u4e0a\u72b9\u53bf', 'level': 3 },
          '1313': { 'id': 1313, 'pid': 1306, 'name': '\u5d07\u4e49\u53bf', 'level': 3 },
          '1314': { 'id': 1314, 'pid': 1306, 'name': '\u5b89\u8fdc\u53bf', 'level': 3 },
          '1315': { 'id': 1315, 'pid': 1306, 'name': '\u9f99\u5357\u53bf', 'level': 3 },
          '1316': { 'id': 1316, 'pid': 1306, 'name': '\u5b9a\u5357\u53bf', 'level': 3 },
          '1317': { 'id': 1317, 'pid': 1306, 'name': '\u5168\u5357\u53bf', 'level': 3 },
          '1318': { 'id': 1318, 'pid': 1306, 'name': '\u5b81\u90fd\u53bf', 'level': 3 },
          '1319': { 'id': 1319, 'pid': 1306, 'name': '\u4e8e\u90fd\u53bf', 'level': 3 },
          '1320': { 'id': 1320, 'pid': 1306, 'name': '\u5174\u56fd\u53bf', 'level': 3 },
          '1321': { 'id': 1321, 'pid': 1306, 'name': '\u4f1a\u660c\u53bf', 'level': 3 },
          '1322': { 'id': 1322, 'pid': 1306, 'name': '\u5bfb\u4e4c\u53bf', 'level': 3 },
          '1323': { 'id': 1323, 'pid': 1306, 'name': '\u77f3\u57ce\u53bf', 'level': 3 },
          '1324': { 'id': 1324, 'pid': 1306, 'name': '\u745e\u91d1\u5e02', 'level': 3 }
        }
      },
      '1325': {
        'id': 1325,
        'pid': 1263,
        'name': '\u5409\u5b89\u5e02',
        'level': 2,
        'region': {
          '1326': { 'id': 1326, 'pid': 1325, 'name': '\u5409\u5dde\u533a', 'level': 3 },
          '1327': { 'id': 1327, 'pid': 1325, 'name': '\u9752\u539f\u533a', 'level': 3 },
          '1328': { 'id': 1328, 'pid': 1325, 'name': '\u5409\u5b89\u53bf', 'level': 3 },
          '1329': { 'id': 1329, 'pid': 1325, 'name': '\u5409\u6c34\u53bf', 'level': 3 },
          '1330': { 'id': 1330, 'pid': 1325, 'name': '\u5ce1\u6c5f\u53bf', 'level': 3 },
          '1331': { 'id': 1331, 'pid': 1325, 'name': '\u65b0\u5e72\u53bf', 'level': 3 },
          '1332': { 'id': 1332, 'pid': 1325, 'name': '\u6c38\u4e30\u53bf', 'level': 3 },
          '1333': { 'id': 1333, 'pid': 1325, 'name': '\u6cf0\u548c\u53bf', 'level': 3 },
          '1334': { 'id': 1334, 'pid': 1325, 'name': '\u9042\u5ddd\u53bf', 'level': 3 },
          '1335': { 'id': 1335, 'pid': 1325, 'name': '\u4e07\u5b89\u53bf', 'level': 3 },
          '1336': { 'id': 1336, 'pid': 1325, 'name': '\u5b89\u798f\u53bf', 'level': 3 },
          '1337': { 'id': 1337, 'pid': 1325, 'name': '\u6c38\u65b0\u53bf', 'level': 3 },
          '1338': { 'id': 1338, 'pid': 1325, 'name': '\u4e95\u5188\u5c71\u5e02', 'level': 3 }
        }
      },
      '1339': {
        'id': 1339,
        'pid': 1263,
        'name': '\u5b9c\u6625\u5e02',
        'level': 2,
        'region': {
          '1340': { 'id': 1340, 'pid': 1339, 'name': '\u8881\u5dde\u533a', 'level': 3 },
          '1341': { 'id': 1341, 'pid': 1339, 'name': '\u5949\u65b0\u53bf', 'level': 3 },
          '1342': { 'id': 1342, 'pid': 1339, 'name': '\u4e07\u8f7d\u53bf', 'level': 3 },
          '1343': { 'id': 1343, 'pid': 1339, 'name': '\u4e0a\u9ad8\u53bf', 'level': 3 },
          '1344': { 'id': 1344, 'pid': 1339, 'name': '\u5b9c\u4e30\u53bf', 'level': 3 },
          '1345': { 'id': 1345, 'pid': 1339, 'name': '\u9756\u5b89\u53bf', 'level': 3 },
          '1346': { 'id': 1346, 'pid': 1339, 'name': '\u94dc\u9f13\u53bf', 'level': 3 },
          '1347': { 'id': 1347, 'pid': 1339, 'name': '\u4e30\u57ce\u5e02', 'level': 3 },
          '1348': { 'id': 1348, 'pid': 1339, 'name': '\u6a1f\u6811\u5e02', 'level': 3 },
          '1349': { 'id': 1349, 'pid': 1339, 'name': '\u9ad8\u5b89\u5e02', 'level': 3 }
        }
      },
      '1350': {
        'id': 1350,
        'pid': 1263,
        'name': '\u629a\u5dde\u5e02',
        'level': 2,
        'region': {
          '1351': { 'id': 1351, 'pid': 1350, 'name': '\u4e34\u5ddd\u533a', 'level': 3 },
          '1352': { 'id': 1352, 'pid': 1350, 'name': '\u5357\u57ce\u53bf', 'level': 3 },
          '1353': { 'id': 1353, 'pid': 1350, 'name': '\u9ece\u5ddd\u53bf', 'level': 3 },
          '1354': { 'id': 1354, 'pid': 1350, 'name': '\u5357\u4e30\u53bf', 'level': 3 },
          '1355': { 'id': 1355, 'pid': 1350, 'name': '\u5d07\u4ec1\u53bf', 'level': 3 },
          '1356': { 'id': 1356, 'pid': 1350, 'name': '\u4e50\u5b89\u53bf', 'level': 3 },
          '1357': { 'id': 1357, 'pid': 1350, 'name': '\u5b9c\u9ec4\u53bf', 'level': 3 },
          '1358': { 'id': 1358, 'pid': 1350, 'name': '\u91d1\u6eaa\u53bf', 'level': 3 },
          '1359': { 'id': 1359, 'pid': 1350, 'name': '\u8d44\u6eaa\u53bf', 'level': 3 },
          '1360': { 'id': 1360, 'pid': 1350, 'name': '\u4e1c\u4e61\u53bf', 'level': 3 },
          '1361': { 'id': 1361, 'pid': 1350, 'name': '\u5e7f\u660c\u53bf', 'level': 3 }
        }
      },
      '1362': {
        'id': 1362,
        'pid': 1263,
        'name': '\u4e0a\u9976\u5e02',
        'level': 2,
        'region': {
          '1363': { 'id': 1363, 'pid': 1362, 'name': '\u4fe1\u5dde\u533a', 'level': 3 },
          '1364': { 'id': 1364, 'pid': 1362, 'name': '\u4e0a\u9976\u53bf', 'level': 3 },
          '1365': { 'id': 1365, 'pid': 1362, 'name': '\u5e7f\u4e30\u53bf', 'level': 3 },
          '1366': { 'id': 1366, 'pid': 1362, 'name': '\u7389\u5c71\u53bf', 'level': 3 },
          '1367': { 'id': 1367, 'pid': 1362, 'name': '\u94c5\u5c71\u53bf', 'level': 3 },
          '1368': { 'id': 1368, 'pid': 1362, 'name': '\u6a2a\u5cf0\u53bf', 'level': 3 },
          '1369': { 'id': 1369, 'pid': 1362, 'name': '\u5f0b\u9633\u53bf', 'level': 3 },
          '1370': { 'id': 1370, 'pid': 1362, 'name': '\u4f59\u5e72\u53bf', 'level': 3 },
          '1371': { 'id': 1371, 'pid': 1362, 'name': '\u9131\u9633\u53bf', 'level': 3 },
          '1372': { 'id': 1372, 'pid': 1362, 'name': '\u4e07\u5e74\u53bf', 'level': 3 },
          '1373': { 'id': 1373, 'pid': 1362, 'name': '\u5a7a\u6e90\u53bf', 'level': 3 },
          '1374': { 'id': 1374, 'pid': 1362, 'name': '\u5fb7\u5174\u5e02', 'level': 3 }
        }
      }
    }
  },
  '1375': {
    'id': 1375, 'pid': 0, 'name': '\u5c71\u4e1c\u7701', 'level': 1, 'city': {
      '1376': {
        'id': 1376,
        'pid': 1375,
        'name': '\u6d4e\u5357\u5e02',
        'level': 2,
        'region': {
          '1377': { 'id': 1377, 'pid': 1376, 'name': '\u5386\u4e0b\u533a', 'level': 3 },
          '1378': { 'id': 1378, 'pid': 1376, 'name': '\u5e02\u4e2d\u533a', 'level': 3 },
          '1379': { 'id': 1379, 'pid': 1376, 'name': '\u69d0\u836b\u533a', 'level': 3 },
          '1380': { 'id': 1380, 'pid': 1376, 'name': '\u5929\u6865\u533a', 'level': 3 },
          '1381': { 'id': 1381, 'pid': 1376, 'name': '\u5386\u57ce\u533a', 'level': 3 },
          '1382': { 'id': 1382, 'pid': 1376, 'name': '\u957f\u6e05\u533a', 'level': 3 },
          '1383': { 'id': 1383, 'pid': 1376, 'name': '\u5e73\u9634\u53bf', 'level': 3 },
          '1384': { 'id': 1384, 'pid': 1376, 'name': '\u6d4e\u9633\u53bf', 'level': 3 },
          '1385': { 'id': 1385, 'pid': 1376, 'name': '\u5546\u6cb3\u53bf', 'level': 3 },
          '1386': { 'id': 1386, 'pid': 1376, 'name': '\u7ae0\u4e18\u5e02', 'level': 3 }
        }
      },
      '1387': {
        'id': 1387,
        'pid': 1375,
        'name': '\u9752\u5c9b\u5e02',
        'level': 2,
        'region': {
          '1388': { 'id': 1388, 'pid': 1387, 'name': '\u5e02\u5357\u533a', 'level': 3 },
          '1389': { 'id': 1389, 'pid': 1387, 'name': '\u5e02\u5317\u533a', 'level': 3 },
          '1390': { 'id': 1390, 'pid': 1387, 'name': '\u9ec4\u5c9b\u533a', 'level': 3 },
          '1391': { 'id': 1391, 'pid': 1387, 'name': '\u5d02\u5c71\u533a', 'level': 3 },
          '1392': { 'id': 1392, 'pid': 1387, 'name': '\u674e\u6ca7\u533a', 'level': 3 },
          '1393': { 'id': 1393, 'pid': 1387, 'name': '\u57ce\u9633\u533a', 'level': 3 },
          '1394': { 'id': 1394, 'pid': 1387, 'name': '\u80f6\u5dde\u5e02', 'level': 3 },
          '1395': { 'id': 1395, 'pid': 1387, 'name': '\u5373\u58a8\u5e02', 'level': 3 },
          '1396': { 'id': 1396, 'pid': 1387, 'name': '\u5e73\u5ea6\u5e02', 'level': 3 },
          '1397': { 'id': 1397, 'pid': 1387, 'name': '\u83b1\u897f\u5e02', 'level': 3 },
          '1398': { 'id': 1398, 'pid': 1387, 'name': '\u897f\u6d77\u5cb8\u65b0\u533a', 'level': 3 }
        }
      },
      '1399': {
        'id': 1399,
        'pid': 1375,
        'name': '\u6dc4\u535a\u5e02',
        'level': 2,
        'region': {
          '1400': { 'id': 1400, 'pid': 1399, 'name': '\u6dc4\u5ddd\u533a', 'level': 3 },
          '1401': { 'id': 1401, 'pid': 1399, 'name': '\u5f20\u5e97\u533a', 'level': 3 },
          '1402': { 'id': 1402, 'pid': 1399, 'name': '\u535a\u5c71\u533a', 'level': 3 },
          '1403': { 'id': 1403, 'pid': 1399, 'name': '\u4e34\u6dc4\u533a', 'level': 3 },
          '1404': { 'id': 1404, 'pid': 1399, 'name': '\u5468\u6751\u533a', 'level': 3 },
          '1405': { 'id': 1405, 'pid': 1399, 'name': '\u6853\u53f0\u53bf', 'level': 3 },
          '1406': { 'id': 1406, 'pid': 1399, 'name': '\u9ad8\u9752\u53bf', 'level': 3 },
          '1407': { 'id': 1407, 'pid': 1399, 'name': '\u6c82\u6e90\u53bf', 'level': 3 }
        }
      },
      '1408': {
        'id': 1408,
        'pid': 1375,
        'name': '\u67a3\u5e84\u5e02',
        'level': 2,
        'region': {
          '1409': { 'id': 1409, 'pid': 1408, 'name': '\u5e02\u4e2d\u533a', 'level': 3 },
          '1410': { 'id': 1410, 'pid': 1408, 'name': '\u859b\u57ce\u533a', 'level': 3 },
          '1411': { 'id': 1411, 'pid': 1408, 'name': '\u5cc4\u57ce\u533a', 'level': 3 },
          '1412': { 'id': 1412, 'pid': 1408, 'name': '\u53f0\u513f\u5e84\u533a', 'level': 3 },
          '1413': { 'id': 1413, 'pid': 1408, 'name': '\u5c71\u4ead\u533a', 'level': 3 },
          '1414': { 'id': 1414, 'pid': 1408, 'name': '\u6ed5\u5dde\u5e02', 'level': 3 }
        }
      },
      '1415': {
        'id': 1415,
        'pid': 1375,
        'name': '\u4e1c\u8425\u5e02',
        'level': 2,
        'region': {
          '1416': { 'id': 1416, 'pid': 1415, 'name': '\u4e1c\u8425\u533a', 'level': 3 },
          '1417': { 'id': 1417, 'pid': 1415, 'name': '\u6cb3\u53e3\u533a', 'level': 3 },
          '1418': { 'id': 1418, 'pid': 1415, 'name': '\u57a6\u5229\u53bf', 'level': 3 },
          '1419': { 'id': 1419, 'pid': 1415, 'name': '\u5229\u6d25\u53bf', 'level': 3 },
          '1420': { 'id': 1420, 'pid': 1415, 'name': '\u5e7f\u9976\u53bf', 'level': 3 }
        }
      },
      '1421': {
        'id': 1421,
        'pid': 1375,
        'name': '\u70df\u53f0\u5e02',
        'level': 2,
        'region': {
          '1422': { 'id': 1422, 'pid': 1421, 'name': '\u829d\u7f58\u533a', 'level': 3 },
          '1423': { 'id': 1423, 'pid': 1421, 'name': '\u798f\u5c71\u533a', 'level': 3 },
          '1424': { 'id': 1424, 'pid': 1421, 'name': '\u725f\u5e73\u533a', 'level': 3 },
          '1425': { 'id': 1425, 'pid': 1421, 'name': '\u83b1\u5c71\u533a', 'level': 3 },
          '1426': { 'id': 1426, 'pid': 1421, 'name': '\u957f\u5c9b\u53bf', 'level': 3 },
          '1427': { 'id': 1427, 'pid': 1421, 'name': '\u9f99\u53e3\u5e02', 'level': 3 },
          '1428': { 'id': 1428, 'pid': 1421, 'name': '\u83b1\u9633\u5e02', 'level': 3 },
          '1429': { 'id': 1429, 'pid': 1421, 'name': '\u83b1\u5dde\u5e02', 'level': 3 },
          '1430': { 'id': 1430, 'pid': 1421, 'name': '\u84ec\u83b1\u5e02', 'level': 3 },
          '1431': { 'id': 1431, 'pid': 1421, 'name': '\u62db\u8fdc\u5e02', 'level': 3 },
          '1432': { 'id': 1432, 'pid': 1421, 'name': '\u6816\u971e\u5e02', 'level': 3 },
          '1433': { 'id': 1433, 'pid': 1421, 'name': '\u6d77\u9633\u5e02', 'level': 3 }
        }
      },
      '1434': {
        'id': 1434,
        'pid': 1375,
        'name': '\u6f4d\u574a\u5e02',
        'level': 2,
        'region': {
          '1435': { 'id': 1435, 'pid': 1434, 'name': '\u6f4d\u57ce\u533a', 'level': 3 },
          '1436': { 'id': 1436, 'pid': 1434, 'name': '\u5bd2\u4ead\u533a', 'level': 3 },
          '1437': { 'id': 1437, 'pid': 1434, 'name': '\u574a\u5b50\u533a', 'level': 3 },
          '1438': { 'id': 1438, 'pid': 1434, 'name': '\u594e\u6587\u533a', 'level': 3 },
          '1439': { 'id': 1439, 'pid': 1434, 'name': '\u4e34\u6710\u53bf', 'level': 3 },
          '1440': { 'id': 1440, 'pid': 1434, 'name': '\u660c\u4e50\u53bf', 'level': 3 },
          '1441': { 'id': 1441, 'pid': 1434, 'name': '\u9752\u5dde\u5e02', 'level': 3 },
          '1442': { 'id': 1442, 'pid': 1434, 'name': '\u8bf8\u57ce\u5e02', 'level': 3 },
          '1443': { 'id': 1443, 'pid': 1434, 'name': '\u5bff\u5149\u5e02', 'level': 3 },
          '1444': { 'id': 1444, 'pid': 1434, 'name': '\u5b89\u4e18\u5e02', 'level': 3 },
          '1445': { 'id': 1445, 'pid': 1434, 'name': '\u9ad8\u5bc6\u5e02', 'level': 3 },
          '1446': { 'id': 1446, 'pid': 1434, 'name': '\u660c\u9091\u5e02', 'level': 3 }
        }
      },
      '1447': {
        'id': 1447,
        'pid': 1375,
        'name': '\u6d4e\u5b81\u5e02',
        'level': 2,
        'region': {
          '1448': { 'id': 1448, 'pid': 1447, 'name': '\u4efb\u57ce\u533a', 'level': 3 },
          '1449': { 'id': 1449, 'pid': 1447, 'name': '\u5156\u5dde\u533a', 'level': 3 },
          '1450': { 'id': 1450, 'pid': 1447, 'name': '\u5fae\u5c71\u53bf', 'level': 3 },
          '1451': { 'id': 1451, 'pid': 1447, 'name': '\u9c7c\u53f0\u53bf', 'level': 3 },
          '1452': { 'id': 1452, 'pid': 1447, 'name': '\u91d1\u4e61\u53bf', 'level': 3 },
          '1453': { 'id': 1453, 'pid': 1447, 'name': '\u5609\u7965\u53bf', 'level': 3 },
          '1454': { 'id': 1454, 'pid': 1447, 'name': '\u6c76\u4e0a\u53bf', 'level': 3 },
          '1455': { 'id': 1455, 'pid': 1447, 'name': '\u6cd7\u6c34\u53bf', 'level': 3 },
          '1456': { 'id': 1456, 'pid': 1447, 'name': '\u6881\u5c71\u53bf', 'level': 3 },
          '1457': { 'id': 1457, 'pid': 1447, 'name': '\u66f2\u961c\u5e02', 'level': 3 },
          '1458': { 'id': 1458, 'pid': 1447, 'name': '\u90b9\u57ce\u5e02', 'level': 3 }
        }
      },
      '1459': {
        'id': 1459,
        'pid': 1375,
        'name': '\u6cf0\u5b89\u5e02',
        'level': 2,
        'region': {
          '1460': { 'id': 1460, 'pid': 1459, 'name': '\u6cf0\u5c71\u533a', 'level': 3 },
          '1461': { 'id': 1461, 'pid': 1459, 'name': '\u5cb1\u5cb3\u533a', 'level': 3 },
          '1462': { 'id': 1462, 'pid': 1459, 'name': '\u5b81\u9633\u53bf', 'level': 3 },
          '1463': { 'id': 1463, 'pid': 1459, 'name': '\u4e1c\u5e73\u53bf', 'level': 3 },
          '1464': { 'id': 1464, 'pid': 1459, 'name': '\u65b0\u6cf0\u5e02', 'level': 3 },
          '1465': { 'id': 1465, 'pid': 1459, 'name': '\u80a5\u57ce\u5e02', 'level': 3 }
        }
      },
      '1466': {
        'id': 1466,
        'pid': 1375,
        'name': '\u5a01\u6d77\u5e02',
        'level': 2,
        'region': {
          '1467': { 'id': 1467, 'pid': 1466, 'name': '\u73af\u7fe0\u533a', 'level': 3 },
          '1468': { 'id': 1468, 'pid': 1466, 'name': '\u6587\u767b\u533a', 'level': 3 },
          '1469': { 'id': 1469, 'pid': 1466, 'name': '\u8363\u6210\u5e02', 'level': 3 },
          '1470': { 'id': 1470, 'pid': 1466, 'name': '\u4e73\u5c71\u5e02', 'level': 3 }
        }
      },
      '1471': {
        'id': 1471,
        'pid': 1375,
        'name': '\u65e5\u7167\u5e02',
        'level': 2,
        'region': {
          '1472': { 'id': 1472, 'pid': 1471, 'name': '\u4e1c\u6e2f\u533a', 'level': 3 },
          '1473': { 'id': 1473, 'pid': 1471, 'name': '\u5c9a\u5c71\u533a', 'level': 3 },
          '1474': { 'id': 1474, 'pid': 1471, 'name': '\u4e94\u83b2\u53bf', 'level': 3 },
          '1475': { 'id': 1475, 'pid': 1471, 'name': '\u8392\u53bf', 'level': 3 }
        }
      },
      '1476': {
        'id': 1476,
        'pid': 1375,
        'name': '\u83b1\u829c\u5e02',
        'level': 2,
        'region': {
          '1477': { 'id': 1477, 'pid': 1476, 'name': '\u83b1\u57ce\u533a', 'level': 3 },
          '1478': { 'id': 1478, 'pid': 1476, 'name': '\u94a2\u57ce\u533a', 'level': 3 }
        }
      },
      '1479': {
        'id': 1479,
        'pid': 1375,
        'name': '\u4e34\u6c82\u5e02',
        'level': 2,
        'region': {
          '1480': { 'id': 1480, 'pid': 1479, 'name': '\u5170\u5c71\u533a', 'level': 3 },
          '1481': { 'id': 1481, 'pid': 1479, 'name': '\u7f57\u5e84\u533a', 'level': 3 },
          '1482': { 'id': 1482, 'pid': 1479, 'name': '\u6cb3\u4e1c\u533a', 'level': 3 },
          '1483': { 'id': 1483, 'pid': 1479, 'name': '\u6c82\u5357\u53bf', 'level': 3 },
          '1484': { 'id': 1484, 'pid': 1479, 'name': '\u90ef\u57ce\u53bf', 'level': 3 },
          '1485': { 'id': 1485, 'pid': 1479, 'name': '\u6c82\u6c34\u53bf', 'level': 3 },
          '1486': { 'id': 1486, 'pid': 1479, 'name': '\u5170\u9675\u53bf', 'level': 3 },
          '1487': { 'id': 1487, 'pid': 1479, 'name': '\u8d39\u53bf', 'level': 3 },
          '1488': { 'id': 1488, 'pid': 1479, 'name': '\u5e73\u9091\u53bf', 'level': 3 },
          '1489': { 'id': 1489, 'pid': 1479, 'name': '\u8392\u5357\u53bf', 'level': 3 },
          '1490': { 'id': 1490, 'pid': 1479, 'name': '\u8499\u9634\u53bf', 'level': 3 },
          '1491': { 'id': 1491, 'pid': 1479, 'name': '\u4e34\u6cad\u53bf', 'level': 3 }
        }
      },
      '1492': {
        'id': 1492,
        'pid': 1375,
        'name': '\u5fb7\u5dde\u5e02',
        'level': 2,
        'region': {
          '1493': { 'id': 1493, 'pid': 1492, 'name': '\u5fb7\u57ce\u533a', 'level': 3 },
          '1494': { 'id': 1494, 'pid': 1492, 'name': '\u9675\u57ce\u533a', 'level': 3 },
          '1495': { 'id': 1495, 'pid': 1492, 'name': '\u5b81\u6d25\u53bf', 'level': 3 },
          '1496': { 'id': 1496, 'pid': 1492, 'name': '\u5e86\u4e91\u53bf', 'level': 3 },
          '1497': { 'id': 1497, 'pid': 1492, 'name': '\u4e34\u9091\u53bf', 'level': 3 },
          '1498': { 'id': 1498, 'pid': 1492, 'name': '\u9f50\u6cb3\u53bf', 'level': 3 },
          '1499': { 'id': 1499, 'pid': 1492, 'name': '\u5e73\u539f\u53bf', 'level': 3 },
          '1500': { 'id': 1500, 'pid': 1492, 'name': '\u590f\u6d25\u53bf', 'level': 3 },
          '1501': { 'id': 1501, 'pid': 1492, 'name': '\u6b66\u57ce\u53bf', 'level': 3 },
          '1502': { 'id': 1502, 'pid': 1492, 'name': '\u4e50\u9675\u5e02', 'level': 3 },
          '1503': { 'id': 1503, 'pid': 1492, 'name': '\u79b9\u57ce\u5e02', 'level': 3 }
        }
      },
      '1504': {
        'id': 1504,
        'pid': 1375,
        'name': '\u804a\u57ce\u5e02',
        'level': 2,
        'region': {
          '1505': { 'id': 1505, 'pid': 1504, 'name': '\u4e1c\u660c\u5e9c\u533a', 'level': 3 },
          '1506': { 'id': 1506, 'pid': 1504, 'name': '\u9633\u8c37\u53bf', 'level': 3 },
          '1507': { 'id': 1507, 'pid': 1504, 'name': '\u8398\u53bf', 'level': 3 },
          '1508': { 'id': 1508, 'pid': 1504, 'name': '\u830c\u5e73\u53bf', 'level': 3 },
          '1509': { 'id': 1509, 'pid': 1504, 'name': '\u4e1c\u963f\u53bf', 'level': 3 },
          '1510': { 'id': 1510, 'pid': 1504, 'name': '\u51a0\u53bf', 'level': 3 },
          '1511': { 'id': 1511, 'pid': 1504, 'name': '\u9ad8\u5510\u53bf', 'level': 3 },
          '1512': { 'id': 1512, 'pid': 1504, 'name': '\u4e34\u6e05\u5e02', 'level': 3 }
        }
      },
      '1513': {
        'id': 1513,
        'pid': 1375,
        'name': '\u6ee8\u5dde\u5e02',
        'level': 2,
        'region': {
          '1514': { 'id': 1514, 'pid': 1513, 'name': '\u6ee8\u57ce\u533a', 'level': 3 },
          '1515': { 'id': 1515, 'pid': 1513, 'name': '\u6cbe\u5316\u533a', 'level': 3 },
          '1516': { 'id': 1516, 'pid': 1513, 'name': '\u60e0\u6c11\u53bf', 'level': 3 },
          '1517': { 'id': 1517, 'pid': 1513, 'name': '\u9633\u4fe1\u53bf', 'level': 3 },
          '1518': { 'id': 1518, 'pid': 1513, 'name': '\u65e0\u68e3\u53bf', 'level': 3 },
          '1519': { 'id': 1519, 'pid': 1513, 'name': '\u535a\u5174\u53bf', 'level': 3 },
          '1520': { 'id': 1520, 'pid': 1513, 'name': '\u90b9\u5e73\u53bf', 'level': 3 },
          '1521': { 'id': 1521, 'pid': 1513, 'name': '\u5317\u6d77\u65b0\u533a', 'level': 3 }
        }
      },
      '1522': {
        'id': 1522,
        'pid': 1375,
        'name': '\u83cf\u6cfd\u5e02',
        'level': 2,
        'region': {
          '1523': { 'id': 1523, 'pid': 1522, 'name': '\u7261\u4e39\u533a', 'level': 3 },
          '1524': { 'id': 1524, 'pid': 1522, 'name': '\u66f9\u53bf', 'level': 3 },
          '1525': { 'id': 1525, 'pid': 1522, 'name': '\u5355\u53bf', 'level': 3 },
          '1526': { 'id': 1526, 'pid': 1522, 'name': '\u6210\u6b66\u53bf', 'level': 3 },
          '1527': { 'id': 1527, 'pid': 1522, 'name': '\u5de8\u91ce\u53bf', 'level': 3 },
          '1528': { 'id': 1528, 'pid': 1522, 'name': '\u90d3\u57ce\u53bf', 'level': 3 },
          '1529': { 'id': 1529, 'pid': 1522, 'name': '\u9104\u57ce\u53bf', 'level': 3 },
          '1530': { 'id': 1530, 'pid': 1522, 'name': '\u5b9a\u9676\u53bf', 'level': 3 },
          '1531': { 'id': 1531, 'pid': 1522, 'name': '\u4e1c\u660e\u53bf', 'level': 3 }
        }
      }
    }
  },
  '1532': {
    'id': 1532, 'pid': 0, 'name': '\u6cb3\u5357\u7701', 'level': 1, 'city': {
      '1533': {
        'id': 1533,
        'pid': 1532,
        'name': '\u90d1\u5dde\u5e02',
        'level': 2,
        'region': {
          '1534': { 'id': 1534, 'pid': 1533, 'name': '\u4e2d\u539f\u533a', 'level': 3 },
          '1535': { 'id': 1535, 'pid': 1533, 'name': '\u4e8c\u4e03\u533a', 'level': 3 },
          '1536': { 'id': 1536, 'pid': 1533, 'name': '\u7ba1\u57ce\u56de\u65cf\u533a', 'level': 3 },
          '1537': { 'id': 1537, 'pid': 1533, 'name': '\u91d1\u6c34\u533a', 'level': 3 },
          '1538': { 'id': 1538, 'pid': 1533, 'name': '\u4e0a\u8857\u533a', 'level': 3 },
          '1539': { 'id': 1539, 'pid': 1533, 'name': '\u60e0\u6d4e\u533a', 'level': 3 },
          '1540': { 'id': 1540, 'pid': 1533, 'name': '\u4e2d\u725f\u53bf', 'level': 3 },
          '1541': { 'id': 1541, 'pid': 1533, 'name': '\u5de9\u4e49\u5e02', 'level': 3 },
          '1542': { 'id': 1542, 'pid': 1533, 'name': '\u8365\u9633\u5e02', 'level': 3 },
          '1543': { 'id': 1543, 'pid': 1533, 'name': '\u65b0\u5bc6\u5e02', 'level': 3 },
          '1544': { 'id': 1544, 'pid': 1533, 'name': '\u65b0\u90d1\u5e02', 'level': 3 },
          '1545': { 'id': 1545, 'pid': 1533, 'name': '\u767b\u5c01\u5e02', 'level': 3 }
        }
      },
      '1546': {
        'id': 1546,
        'pid': 1532,
        'name': '\u5f00\u5c01\u5e02',
        'level': 2,
        'region': {
          '1547': { 'id': 1547, 'pid': 1546, 'name': '\u9f99\u4ead\u533a', 'level': 3 },
          '1548': { 'id': 1548, 'pid': 1546, 'name': '\u987a\u6cb3\u56de\u65cf\u533a', 'level': 3 },
          '1549': { 'id': 1549, 'pid': 1546, 'name': '\u9f13\u697c\u533a', 'level': 3 },
          '1550': { 'id': 1550, 'pid': 1546, 'name': '\u79b9\u738b\u53f0\u533a', 'level': 3 },
          '1551': { 'id': 1551, 'pid': 1546, 'name': '\u7965\u7b26\u533a', 'level': 3 },
          '1552': { 'id': 1552, 'pid': 1546, 'name': '\u675e\u53bf', 'level': 3 },
          '1553': { 'id': 1553, 'pid': 1546, 'name': '\u901a\u8bb8\u53bf', 'level': 3 },
          '1554': { 'id': 1554, 'pid': 1546, 'name': '\u5c09\u6c0f\u53bf', 'level': 3 },
          '1555': { 'id': 1555, 'pid': 1546, 'name': '\u5170\u8003\u53bf', 'level': 3 }
        }
      },
      '1556': {
        'id': 1556, 'pid': 1532, 'name': '\u6d1b\u9633\u5e02', 'level': 2, 'region': {
          '1557': { 'id': 1557, 'pid': 1556, 'name': '\u8001\u57ce\u533a', 'level': 3 },
          '1558': { 'id': 1558, 'pid': 1556, 'name': '\u897f\u5de5\u533a', 'level': 3 },
          '1559': { 'id': 1559, 'pid': 1556, 'name': '\u700d\u6cb3\u56de\u65cf\u533a', 'level': 3 },
          '1560': { 'id': 1560, 'pid': 1556, 'name': '\u6da7\u897f\u533a', 'level': 3 },
          '1561': { 'id': 1561, 'pid': 1556, 'name': '\u5409\u5229\u533a', 'level': 3 },
          '1562': { 'id': 1562, 'pid': 1556, 'name': '\u6d1b\u9f99\u533a', 'level': 3 },
          '1563': { 'id': 1563, 'pid': 1556, 'name': '\u5b5f\u6d25\u53bf', 'level': 3 },
          '1564': { 'id': 1564, 'pid': 1556, 'name': '\u65b0\u5b89\u53bf', 'level': 3 },
          '1565': { 'id': 1565, 'pid': 1556, 'name': '\u683e\u5ddd\u53bf', 'level': 3 },
          '1566': { 'id': 1566, 'pid': 1556, 'name': '\u5d69\u53bf', 'level': 3 },
          '1567': { 'id': 1567, 'pid': 1556, 'name': '\u6c5d\u9633\u53bf', 'level': 3 },
          '1568': { 'id': 1568, 'pid': 1556, 'name': '\u5b9c\u9633\u53bf', 'level': 3 },
          '1569': { 'id': 1569, 'pid': 1556, 'name': '\u6d1b\u5b81\u53bf', 'level': 3 },
          '1570': { 'id': 1570, 'pid': 1556, 'name': '\u4f0a\u5ddd\u53bf', 'level': 3 },
          '1571': { 'id': 1571, 'pid': 1556, 'name': '\u5043\u5e08\u5e02', 'level': 3 }
        }
      },
      '1572': {
        'id': 1572,
        'pid': 1532,
        'name': '\u5e73\u9876\u5c71\u5e02',
        'level': 2,
        'region': {
          '1573': { 'id': 1573, 'pid': 1572, 'name': '\u65b0\u534e\u533a', 'level': 3 },
          '1574': { 'id': 1574, 'pid': 1572, 'name': '\u536b\u4e1c\u533a', 'level': 3 },
          '1575': { 'id': 1575, 'pid': 1572, 'name': '\u77f3\u9f99\u533a', 'level': 3 },
          '1576': { 'id': 1576, 'pid': 1572, 'name': '\u6e5b\u6cb3\u533a', 'level': 3 },
          '1577': { 'id': 1577, 'pid': 1572, 'name': '\u5b9d\u4e30\u53bf', 'level': 3 },
          '1578': { 'id': 1578, 'pid': 1572, 'name': '\u53f6\u53bf', 'level': 3 },
          '1579': { 'id': 1579, 'pid': 1572, 'name': '\u9c81\u5c71\u53bf', 'level': 3 },
          '1580': { 'id': 1580, 'pid': 1572, 'name': '\u90cf\u53bf', 'level': 3 },
          '1581': { 'id': 1581, 'pid': 1572, 'name': '\u821e\u94a2\u5e02', 'level': 3 },
          '1582': { 'id': 1582, 'pid': 1572, 'name': '\u6c5d\u5dde\u5e02', 'level': 3 }
        }
      },
      '1583': {
        'id': 1583,
        'pid': 1532,
        'name': '\u5b89\u9633\u5e02',
        'level': 2,
        'region': {
          '1584': { 'id': 1584, 'pid': 1583, 'name': '\u6587\u5cf0\u533a', 'level': 3 },
          '1585': { 'id': 1585, 'pid': 1583, 'name': '\u5317\u5173\u533a', 'level': 3 },
          '1586': { 'id': 1586, 'pid': 1583, 'name': '\u6bb7\u90fd\u533a', 'level': 3 },
          '1587': { 'id': 1587, 'pid': 1583, 'name': '\u9f99\u5b89\u533a', 'level': 3 },
          '1588': { 'id': 1588, 'pid': 1583, 'name': '\u5b89\u9633\u53bf', 'level': 3 },
          '1589': { 'id': 1589, 'pid': 1583, 'name': '\u6c64\u9634\u53bf', 'level': 3 },
          '1590': { 'id': 1590, 'pid': 1583, 'name': '\u6ed1\u53bf', 'level': 3 },
          '1591': { 'id': 1591, 'pid': 1583, 'name': '\u5185\u9ec4\u53bf', 'level': 3 },
          '1592': { 'id': 1592, 'pid': 1583, 'name': '\u6797\u5dde\u5e02', 'level': 3 }
        }
      },
      '1593': {
        'id': 1593,
        'pid': 1532,
        'name': '\u9e64\u58c1\u5e02',
        'level': 2,
        'region': {
          '1594': { 'id': 1594, 'pid': 1593, 'name': '\u9e64\u5c71\u533a', 'level': 3 },
          '1595': { 'id': 1595, 'pid': 1593, 'name': '\u5c71\u57ce\u533a', 'level': 3 },
          '1596': { 'id': 1596, 'pid': 1593, 'name': '\u6dc7\u6ee8\u533a', 'level': 3 },
          '1597': { 'id': 1597, 'pid': 1593, 'name': '\u6d5a\u53bf', 'level': 3 },
          '1598': { 'id': 1598, 'pid': 1593, 'name': '\u6dc7\u53bf', 'level': 3 }
        }
      },
      '1599': {
        'id': 1599,
        'pid': 1532,
        'name': '\u65b0\u4e61\u5e02',
        'level': 2,
        'region': {
          '1600': { 'id': 1600, 'pid': 1599, 'name': '\u7ea2\u65d7\u533a', 'level': 3 },
          '1601': { 'id': 1601, 'pid': 1599, 'name': '\u536b\u6ee8\u533a', 'level': 3 },
          '1602': { 'id': 1602, 'pid': 1599, 'name': '\u51e4\u6cc9\u533a', 'level': 3 },
          '1603': { 'id': 1603, 'pid': 1599, 'name': '\u7267\u91ce\u533a', 'level': 3 },
          '1604': { 'id': 1604, 'pid': 1599, 'name': '\u65b0\u4e61\u53bf', 'level': 3 },
          '1605': { 'id': 1605, 'pid': 1599, 'name': '\u83b7\u5609\u53bf', 'level': 3 },
          '1606': { 'id': 1606, 'pid': 1599, 'name': '\u539f\u9633\u53bf', 'level': 3 },
          '1607': { 'id': 1607, 'pid': 1599, 'name': '\u5ef6\u6d25\u53bf', 'level': 3 },
          '1608': { 'id': 1608, 'pid': 1599, 'name': '\u5c01\u4e18\u53bf', 'level': 3 },
          '1609': { 'id': 1609, 'pid': 1599, 'name': '\u957f\u57a3\u53bf', 'level': 3 },
          '1610': { 'id': 1610, 'pid': 1599, 'name': '\u536b\u8f89\u5e02', 'level': 3 },
          '1611': { 'id': 1611, 'pid': 1599, 'name': '\u8f89\u53bf\u5e02', 'level': 3 }
        }
      },
      '1612': {
        'id': 1612,
        'pid': 1532,
        'name': '\u7126\u4f5c\u5e02',
        'level': 2,
        'region': {
          '1613': { 'id': 1613, 'pid': 1612, 'name': '\u89e3\u653e\u533a', 'level': 3 },
          '1614': { 'id': 1614, 'pid': 1612, 'name': '\u4e2d\u7ad9\u533a', 'level': 3 },
          '1615': { 'id': 1615, 'pid': 1612, 'name': '\u9a6c\u6751\u533a', 'level': 3 },
          '1616': { 'id': 1616, 'pid': 1612, 'name': '\u5c71\u9633\u533a', 'level': 3 },
          '1617': { 'id': 1617, 'pid': 1612, 'name': '\u4fee\u6b66\u53bf', 'level': 3 },
          '1618': { 'id': 1618, 'pid': 1612, 'name': '\u535a\u7231\u53bf', 'level': 3 },
          '1619': { 'id': 1619, 'pid': 1612, 'name': '\u6b66\u965f\u53bf', 'level': 3 },
          '1620': { 'id': 1620, 'pid': 1612, 'name': '\u6e29\u53bf', 'level': 3 },
          '1621': { 'id': 1621, 'pid': 1612, 'name': '\u6c81\u9633\u5e02', 'level': 3 },
          '1622': { 'id': 1622, 'pid': 1612, 'name': '\u5b5f\u5dde\u5e02', 'level': 3 }
        }
      },
      '1623': {
        'id': 1623,
        'pid': 1532,
        'name': '\u6fee\u9633\u5e02',
        'level': 2,
        'region': {
          '1624': { 'id': 1624, 'pid': 1623, 'name': '\u534e\u9f99\u533a', 'level': 3 },
          '1625': { 'id': 1625, 'pid': 1623, 'name': '\u6e05\u4e30\u53bf', 'level': 3 },
          '1626': { 'id': 1626, 'pid': 1623, 'name': '\u5357\u4e50\u53bf', 'level': 3 },
          '1627': { 'id': 1627, 'pid': 1623, 'name': '\u8303\u53bf', 'level': 3 },
          '1628': { 'id': 1628, 'pid': 1623, 'name': '\u53f0\u524d\u53bf', 'level': 3 },
          '1629': { 'id': 1629, 'pid': 1623, 'name': '\u6fee\u9633\u53bf', 'level': 3 }
        }
      },
      '1630': {
        'id': 1630,
        'pid': 1532,
        'name': '\u8bb8\u660c\u5e02',
        'level': 2,
        'region': {
          '1631': { 'id': 1631, 'pid': 1630, 'name': '\u9b4f\u90fd\u533a', 'level': 3 },
          '1632': { 'id': 1632, 'pid': 1630, 'name': '\u8bb8\u660c\u53bf', 'level': 3 },
          '1633': { 'id': 1633, 'pid': 1630, 'name': '\u9122\u9675\u53bf', 'level': 3 },
          '1634': { 'id': 1634, 'pid': 1630, 'name': '\u8944\u57ce\u53bf', 'level': 3 },
          '1635': { 'id': 1635, 'pid': 1630, 'name': '\u79b9\u5dde\u5e02', 'level': 3 },
          '1636': { 'id': 1636, 'pid': 1630, 'name': '\u957f\u845b\u5e02', 'level': 3 }
        }
      },
      '1637': {
        'id': 1637,
        'pid': 1532,
        'name': '\u6f2f\u6cb3\u5e02',
        'level': 2,
        'region': {
          '1638': { 'id': 1638, 'pid': 1637, 'name': '\u6e90\u6c47\u533a', 'level': 3 },
          '1639': { 'id': 1639, 'pid': 1637, 'name': '\u90fe\u57ce\u533a', 'level': 3 },
          '1640': { 'id': 1640, 'pid': 1637, 'name': '\u53ec\u9675\u533a', 'level': 3 },
          '1641': { 'id': 1641, 'pid': 1637, 'name': '\u821e\u9633\u53bf', 'level': 3 },
          '1642': { 'id': 1642, 'pid': 1637, 'name': '\u4e34\u988d\u53bf', 'level': 3 }
        }
      },
      '1643': {
        'id': 1643,
        'pid': 1532,
        'name': '\u4e09\u95e8\u5ce1\u5e02',
        'level': 2,
        'region': {
          '1644': { 'id': 1644, 'pid': 1643, 'name': '\u6e56\u6ee8\u533a', 'level': 3 },
          '1645': { 'id': 1645, 'pid': 1643, 'name': '\u6e11\u6c60\u53bf', 'level': 3 },
          '1646': { 'id': 1646, 'pid': 1643, 'name': '\u9655\u53bf', 'level': 3 },
          '1647': { 'id': 1647, 'pid': 1643, 'name': '\u5362\u6c0f\u53bf', 'level': 3 },
          '1648': { 'id': 1648, 'pid': 1643, 'name': '\u4e49\u9a6c\u5e02', 'level': 3 },
          '1649': { 'id': 1649, 'pid': 1643, 'name': '\u7075\u5b9d\u5e02', 'level': 3 }
        }
      },
      '1650': {
        'id': 1650,
        'pid': 1532,
        'name': '\u5357\u9633\u5e02',
        'level': 2,
        'region': {
          '1651': { 'id': 1651, 'pid': 1650, 'name': '\u5b9b\u57ce\u533a', 'level': 3 },
          '1652': { 'id': 1652, 'pid': 1650, 'name': '\u5367\u9f99\u533a', 'level': 3 },
          '1653': { 'id': 1653, 'pid': 1650, 'name': '\u5357\u53ec\u53bf', 'level': 3 },
          '1654': { 'id': 1654, 'pid': 1650, 'name': '\u65b9\u57ce\u53bf', 'level': 3 },
          '1655': { 'id': 1655, 'pid': 1650, 'name': '\u897f\u5ce1\u53bf', 'level': 3 },
          '1656': { 'id': 1656, 'pid': 1650, 'name': '\u9547\u5e73\u53bf', 'level': 3 },
          '1657': { 'id': 1657, 'pid': 1650, 'name': '\u5185\u4e61\u53bf', 'level': 3 },
          '1658': { 'id': 1658, 'pid': 1650, 'name': '\u6dc5\u5ddd\u53bf', 'level': 3 },
          '1659': { 'id': 1659, 'pid': 1650, 'name': '\u793e\u65d7\u53bf', 'level': 3 },
          '1660': { 'id': 1660, 'pid': 1650, 'name': '\u5510\u6cb3\u53bf', 'level': 3 },
          '1661': { 'id': 1661, 'pid': 1650, 'name': '\u65b0\u91ce\u53bf', 'level': 3 },
          '1662': { 'id': 1662, 'pid': 1650, 'name': '\u6850\u67cf\u53bf', 'level': 3 },
          '1663': { 'id': 1663, 'pid': 1650, 'name': '\u9093\u5dde\u5e02', 'level': 3 }
        }
      },
      '1664': {
        'id': 1664,
        'pid': 1532,
        'name': '\u5546\u4e18\u5e02',
        'level': 2,
        'region': {
          '1665': { 'id': 1665, 'pid': 1664, 'name': '\u6881\u56ed\u533a', 'level': 3 },
          '1666': { 'id': 1666, 'pid': 1664, 'name': '\u7762\u9633\u533a', 'level': 3 },
          '1667': { 'id': 1667, 'pid': 1664, 'name': '\u6c11\u6743\u53bf', 'level': 3 },
          '1668': { 'id': 1668, 'pid': 1664, 'name': '\u7762\u53bf', 'level': 3 },
          '1669': { 'id': 1669, 'pid': 1664, 'name': '\u5b81\u9675\u53bf', 'level': 3 },
          '1670': { 'id': 1670, 'pid': 1664, 'name': '\u67d8\u57ce\u53bf', 'level': 3 },
          '1671': { 'id': 1671, 'pid': 1664, 'name': '\u865e\u57ce\u53bf', 'level': 3 },
          '1672': { 'id': 1672, 'pid': 1664, 'name': '\u590f\u9091\u53bf', 'level': 3 },
          '1673': { 'id': 1673, 'pid': 1664, 'name': '\u6c38\u57ce\u5e02', 'level': 3 }
        }
      },
      '1674': {
        'id': 1674,
        'pid': 1532,
        'name': '\u4fe1\u9633\u5e02',
        'level': 2,
        'region': {
          '1675': { 'id': 1675, 'pid': 1674, 'name': '\u6d49\u6cb3\u533a', 'level': 3 },
          '1676': { 'id': 1676, 'pid': 1674, 'name': '\u5e73\u6865\u533a', 'level': 3 },
          '1677': { 'id': 1677, 'pid': 1674, 'name': '\u7f57\u5c71\u53bf', 'level': 3 },
          '1678': { 'id': 1678, 'pid': 1674, 'name': '\u5149\u5c71\u53bf', 'level': 3 },
          '1679': { 'id': 1679, 'pid': 1674, 'name': '\u65b0\u53bf', 'level': 3 },
          '1680': { 'id': 1680, 'pid': 1674, 'name': '\u5546\u57ce\u53bf', 'level': 3 },
          '1681': { 'id': 1681, 'pid': 1674, 'name': '\u56fa\u59cb\u53bf', 'level': 3 },
          '1682': { 'id': 1682, 'pid': 1674, 'name': '\u6f62\u5ddd\u53bf', 'level': 3 },
          '1683': { 'id': 1683, 'pid': 1674, 'name': '\u6dee\u6ee8\u53bf', 'level': 3 },
          '1684': { 'id': 1684, 'pid': 1674, 'name': '\u606f\u53bf', 'level': 3 }
        }
      },
      '1685': {
        'id': 1685,
        'pid': 1532,
        'name': '\u5468\u53e3\u5e02',
        'level': 2,
        'region': {
          '1686': { 'id': 1686, 'pid': 1685, 'name': '\u5ddd\u6c47\u533a', 'level': 3 },
          '1687': { 'id': 1687, 'pid': 1685, 'name': '\u6276\u6c9f\u53bf', 'level': 3 },
          '1688': { 'id': 1688, 'pid': 1685, 'name': '\u897f\u534e\u53bf', 'level': 3 },
          '1689': { 'id': 1689, 'pid': 1685, 'name': '\u5546\u6c34\u53bf', 'level': 3 },
          '1690': { 'id': 1690, 'pid': 1685, 'name': '\u6c88\u4e18\u53bf', 'level': 3 },
          '1691': { 'id': 1691, 'pid': 1685, 'name': '\u90f8\u57ce\u53bf', 'level': 3 },
          '1692': { 'id': 1692, 'pid': 1685, 'name': '\u6dee\u9633\u53bf', 'level': 3 },
          '1693': { 'id': 1693, 'pid': 1685, 'name': '\u592a\u5eb7\u53bf', 'level': 3 },
          '1694': { 'id': 1694, 'pid': 1685, 'name': '\u9e7f\u9091\u53bf', 'level': 3 },
          '1695': { 'id': 1695, 'pid': 1685, 'name': '\u9879\u57ce\u5e02', 'level': 3 }
        }
      },
      '1696': {
        'id': 1696,
        'pid': 1532,
        'name': '\u9a7b\u9a6c\u5e97\u5e02',
        'level': 2,
        'region': {
          '1697': { 'id': 1697, 'pid': 1696, 'name': '\u9a7f\u57ce\u533a', 'level': 3 },
          '1698': { 'id': 1698, 'pid': 1696, 'name': '\u897f\u5e73\u53bf', 'level': 3 },
          '1699': { 'id': 1699, 'pid': 1696, 'name': '\u4e0a\u8521\u53bf', 'level': 3 },
          '1700': { 'id': 1700, 'pid': 1696, 'name': '\u5e73\u8206\u53bf', 'level': 3 },
          '1701': { 'id': 1701, 'pid': 1696, 'name': '\u6b63\u9633\u53bf', 'level': 3 },
          '1702': { 'id': 1702, 'pid': 1696, 'name': '\u786e\u5c71\u53bf', 'level': 3 },
          '1703': { 'id': 1703, 'pid': 1696, 'name': '\u6ccc\u9633\u53bf', 'level': 3 },
          '1704': { 'id': 1704, 'pid': 1696, 'name': '\u6c5d\u5357\u53bf', 'level': 3 },
          '1705': { 'id': 1705, 'pid': 1696, 'name': '\u9042\u5e73\u53bf', 'level': 3 },
          '1706': { 'id': 1706, 'pid': 1696, 'name': '\u65b0\u8521\u53bf', 'level': 3 }
        }
      },
      '1707': {
        'id': 1707,
        'pid': 1532,
        'name': '\u76f4\u8f96\u53bf\u7ea7',
        'level': 2,
        'region': { '1708': { 'id': 1708, 'pid': 1707, 'name': '\u6d4e\u6e90\u5e02', 'level': 3 }}
      }
    }
  },
  '1709': {
    'id': 1709, 'pid': 0, 'name': '\u6e56\u5317\u7701', 'level': 1, 'city': {
      '1710': {
        'id': 1710,
        'pid': 1709,
        'name': '\u6b66\u6c49\u5e02',
        'level': 2,
        'region': {
          '1711': { 'id': 1711, 'pid': 1710, 'name': '\u6c5f\u5cb8\u533a', 'level': 3 },
          '1712': { 'id': 1712, 'pid': 1710, 'name': '\u6c5f\u6c49\u533a', 'level': 3 },
          '1713': { 'id': 1713, 'pid': 1710, 'name': '\u785a\u53e3\u533a', 'level': 3 },
          '1714': { 'id': 1714, 'pid': 1710, 'name': '\u6c49\u9633\u533a', 'level': 3 },
          '1715': { 'id': 1715, 'pid': 1710, 'name': '\u6b66\u660c\u533a', 'level': 3 },
          '1716': { 'id': 1716, 'pid': 1710, 'name': '\u9752\u5c71\u533a', 'level': 3 },
          '1717': { 'id': 1717, 'pid': 1710, 'name': '\u6d2a\u5c71\u533a', 'level': 3 },
          '1718': { 'id': 1718, 'pid': 1710, 'name': '\u4e1c\u897f\u6e56\u533a', 'level': 3 },
          '1719': { 'id': 1719, 'pid': 1710, 'name': '\u6c49\u5357\u533a', 'level': 3 },
          '1720': { 'id': 1720, 'pid': 1710, 'name': '\u8521\u7538\u533a', 'level': 3 },
          '1721': { 'id': 1721, 'pid': 1710, 'name': '\u6c5f\u590f\u533a', 'level': 3 },
          '1722': { 'id': 1722, 'pid': 1710, 'name': '\u9ec4\u9642\u533a', 'level': 3 },
          '1723': { 'id': 1723, 'pid': 1710, 'name': '\u65b0\u6d32\u533a', 'level': 3 }
        }
      },
      '1724': {
        'id': 1724,
        'pid': 1709,
        'name': '\u9ec4\u77f3\u5e02',
        'level': 2,
        'region': {
          '1725': { 'id': 1725, 'pid': 1724, 'name': '\u9ec4\u77f3\u6e2f\u533a', 'level': 3 },
          '1726': { 'id': 1726, 'pid': 1724, 'name': '\u897f\u585e\u5c71\u533a', 'level': 3 },
          '1727': { 'id': 1727, 'pid': 1724, 'name': '\u4e0b\u9646\u533a', 'level': 3 },
          '1728': { 'id': 1728, 'pid': 1724, 'name': '\u94c1\u5c71\u533a', 'level': 3 },
          '1729': { 'id': 1729, 'pid': 1724, 'name': '\u9633\u65b0\u53bf', 'level': 3 },
          '1730': { 'id': 1730, 'pid': 1724, 'name': '\u5927\u51b6\u5e02', 'level': 3 }
        }
      },
      '1731': {
        'id': 1731,
        'pid': 1709,
        'name': '\u5341\u5830\u5e02',
        'level': 2,
        'region': {
          '1732': { 'id': 1732, 'pid': 1731, 'name': '\u8305\u7bad\u533a', 'level': 3 },
          '1733': { 'id': 1733, 'pid': 1731, 'name': '\u5f20\u6e7e\u533a', 'level': 3 },
          '1734': { 'id': 1734, 'pid': 1731, 'name': '\u90e7\u9633\u533a', 'level': 3 },
          '1735': { 'id': 1735, 'pid': 1731, 'name': '\u90e7\u897f\u53bf', 'level': 3 },
          '1736': { 'id': 1736, 'pid': 1731, 'name': '\u7af9\u5c71\u53bf', 'level': 3 },
          '1737': { 'id': 1737, 'pid': 1731, 'name': '\u7af9\u6eaa\u53bf', 'level': 3 },
          '1738': { 'id': 1738, 'pid': 1731, 'name': '\u623f\u53bf', 'level': 3 },
          '1739': { 'id': 1739, 'pid': 1731, 'name': '\u4e39\u6c5f\u53e3\u5e02', 'level': 3 }
        }
      },
      '1740': {
        'id': 1740,
        'pid': 1709,
        'name': '\u5b9c\u660c\u5e02',
        'level': 2,
        'region': {
          '1741': { 'id': 1741, 'pid': 1740, 'name': '\u897f\u9675\u533a', 'level': 3 },
          '1742': { 'id': 1742, 'pid': 1740, 'name': '\u4f0d\u5bb6\u5c97\u533a', 'level': 3 },
          '1743': { 'id': 1743, 'pid': 1740, 'name': '\u70b9\u519b\u533a', 'level': 3 },
          '1744': { 'id': 1744, 'pid': 1740, 'name': '\u7307\u4ead\u533a', 'level': 3 },
          '1745': { 'id': 1745, 'pid': 1740, 'name': '\u5937\u9675\u533a', 'level': 3 },
          '1746': { 'id': 1746, 'pid': 1740, 'name': '\u8fdc\u5b89\u53bf', 'level': 3 },
          '1747': { 'id': 1747, 'pid': 1740, 'name': '\u5174\u5c71\u53bf', 'level': 3 },
          '1748': { 'id': 1748, 'pid': 1740, 'name': '\u79ed\u5f52\u53bf', 'level': 3 },
          '1749': { 'id': 1749, 'pid': 1740, 'name': '\u957f\u9633\u571f\u5bb6\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '1750': { 'id': 1750, 'pid': 1740, 'name': '\u4e94\u5cf0\u571f\u5bb6\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '1751': { 'id': 1751, 'pid': 1740, 'name': '\u5b9c\u90fd\u5e02', 'level': 3 },
          '1752': { 'id': 1752, 'pid': 1740, 'name': '\u5f53\u9633\u5e02', 'level': 3 },
          '1753': { 'id': 1753, 'pid': 1740, 'name': '\u679d\u6c5f\u5e02', 'level': 3 }
        }
      },
      '1754': {
        'id': 1754,
        'pid': 1709,
        'name': '\u8944\u9633\u5e02',
        'level': 2,
        'region': {
          '1755': { 'id': 1755, 'pid': 1754, 'name': '\u8944\u57ce\u533a', 'level': 3 },
          '1756': { 'id': 1756, 'pid': 1754, 'name': '\u6a0a\u57ce\u533a', 'level': 3 },
          '1757': { 'id': 1757, 'pid': 1754, 'name': '\u8944\u5dde\u533a', 'level': 3 },
          '1758': { 'id': 1758, 'pid': 1754, 'name': '\u5357\u6f33\u53bf', 'level': 3 },
          '1759': { 'id': 1759, 'pid': 1754, 'name': '\u8c37\u57ce\u53bf', 'level': 3 },
          '1760': { 'id': 1760, 'pid': 1754, 'name': '\u4fdd\u5eb7\u53bf', 'level': 3 },
          '1761': { 'id': 1761, 'pid': 1754, 'name': '\u8001\u6cb3\u53e3\u5e02', 'level': 3 },
          '1762': { 'id': 1762, 'pid': 1754, 'name': '\u67a3\u9633\u5e02', 'level': 3 },
          '1763': { 'id': 1763, 'pid': 1754, 'name': '\u5b9c\u57ce\u5e02', 'level': 3 }
        }
      },
      '1764': {
        'id': 1764,
        'pid': 1709,
        'name': '\u9102\u5dde\u5e02',
        'level': 2,
        'region': {
          '1765': { 'id': 1765, 'pid': 1764, 'name': '\u6881\u5b50\u6e56\u533a', 'level': 3 },
          '1766': { 'id': 1766, 'pid': 1764, 'name': '\u534e\u5bb9\u533a', 'level': 3 },
          '1767': { 'id': 1767, 'pid': 1764, 'name': '\u9102\u57ce\u533a', 'level': 3 }
        }
      },
      '1768': {
        'id': 1768,
        'pid': 1709,
        'name': '\u8346\u95e8\u5e02',
        'level': 2,
        'region': {
          '1769': { 'id': 1769, 'pid': 1768, 'name': '\u4e1c\u5b9d\u533a', 'level': 3 },
          '1770': { 'id': 1770, 'pid': 1768, 'name': '\u6387\u5200\u533a', 'level': 3 },
          '1771': { 'id': 1771, 'pid': 1768, 'name': '\u4eac\u5c71\u53bf', 'level': 3 },
          '1772': { 'id': 1772, 'pid': 1768, 'name': '\u6c99\u6d0b\u53bf', 'level': 3 },
          '1773': { 'id': 1773, 'pid': 1768, 'name': '\u949f\u7965\u5e02', 'level': 3 }
        }
      },
      '1774': {
        'id': 1774,
        'pid': 1709,
        'name': '\u5b5d\u611f\u5e02',
        'level': 2,
        'region': {
          '1775': { 'id': 1775, 'pid': 1774, 'name': '\u5b5d\u5357\u533a', 'level': 3 },
          '1776': { 'id': 1776, 'pid': 1774, 'name': '\u5b5d\u660c\u53bf', 'level': 3 },
          '1777': { 'id': 1777, 'pid': 1774, 'name': '\u5927\u609f\u53bf', 'level': 3 },
          '1778': { 'id': 1778, 'pid': 1774, 'name': '\u4e91\u68a6\u53bf', 'level': 3 },
          '1779': { 'id': 1779, 'pid': 1774, 'name': '\u5e94\u57ce\u5e02', 'level': 3 },
          '1780': { 'id': 1780, 'pid': 1774, 'name': '\u5b89\u9646\u5e02', 'level': 3 },
          '1781': { 'id': 1781, 'pid': 1774, 'name': '\u6c49\u5ddd\u5e02', 'level': 3 }
        }
      },
      '1782': {
        'id': 1782,
        'pid': 1709,
        'name': '\u8346\u5dde\u5e02',
        'level': 2,
        'region': {
          '1783': { 'id': 1783, 'pid': 1782, 'name': '\u6c99\u5e02\u533a', 'level': 3 },
          '1784': { 'id': 1784, 'pid': 1782, 'name': '\u8346\u5dde\u533a', 'level': 3 },
          '1785': { 'id': 1785, 'pid': 1782, 'name': '\u516c\u5b89\u53bf', 'level': 3 },
          '1786': { 'id': 1786, 'pid': 1782, 'name': '\u76d1\u5229\u53bf', 'level': 3 },
          '1787': { 'id': 1787, 'pid': 1782, 'name': '\u6c5f\u9675\u53bf', 'level': 3 },
          '1788': { 'id': 1788, 'pid': 1782, 'name': '\u77f3\u9996\u5e02', 'level': 3 },
          '1789': { 'id': 1789, 'pid': 1782, 'name': '\u6d2a\u6e56\u5e02', 'level': 3 },
          '1790': { 'id': 1790, 'pid': 1782, 'name': '\u677e\u6ecb\u5e02', 'level': 3 }
        }
      },
      '1791': {
        'id': 1791,
        'pid': 1709,
        'name': '\u9ec4\u5188\u5e02',
        'level': 2,
        'region': {
          '1792': { 'id': 1792, 'pid': 1791, 'name': '\u9ec4\u5dde\u533a', 'level': 3 },
          '1793': { 'id': 1793, 'pid': 1791, 'name': '\u56e2\u98ce\u53bf', 'level': 3 },
          '1794': { 'id': 1794, 'pid': 1791, 'name': '\u7ea2\u5b89\u53bf', 'level': 3 },
          '1795': { 'id': 1795, 'pid': 1791, 'name': '\u7f57\u7530\u53bf', 'level': 3 },
          '1796': { 'id': 1796, 'pid': 1791, 'name': '\u82f1\u5c71\u53bf', 'level': 3 },
          '1797': { 'id': 1797, 'pid': 1791, 'name': '\u6d60\u6c34\u53bf', 'level': 3 },
          '1798': { 'id': 1798, 'pid': 1791, 'name': '\u8572\u6625\u53bf', 'level': 3 },
          '1799': { 'id': 1799, 'pid': 1791, 'name': '\u9ec4\u6885\u53bf', 'level': 3 },
          '1800': { 'id': 1800, 'pid': 1791, 'name': '\u9ebb\u57ce\u5e02', 'level': 3 },
          '1801': { 'id': 1801, 'pid': 1791, 'name': '\u6b66\u7a74\u5e02', 'level': 3 }
        }
      },
      '1802': {
        'id': 1802,
        'pid': 1709,
        'name': '\u54b8\u5b81\u5e02',
        'level': 2,
        'region': {
          '1803': { 'id': 1803, 'pid': 1802, 'name': '\u54b8\u5b89\u533a', 'level': 3 },
          '1804': { 'id': 1804, 'pid': 1802, 'name': '\u5609\u9c7c\u53bf', 'level': 3 },
          '1805': { 'id': 1805, 'pid': 1802, 'name': '\u901a\u57ce\u53bf', 'level': 3 },
          '1806': { 'id': 1806, 'pid': 1802, 'name': '\u5d07\u9633\u53bf', 'level': 3 },
          '1807': { 'id': 1807, 'pid': 1802, 'name': '\u901a\u5c71\u53bf', 'level': 3 },
          '1808': { 'id': 1808, 'pid': 1802, 'name': '\u8d64\u58c1\u5e02', 'level': 3 }
        }
      },
      '1809': {
        'id': 1809,
        'pid': 1709,
        'name': '\u968f\u5dde\u5e02',
        'level': 2,
        'region': {
          '1810': { 'id': 1810, 'pid': 1809, 'name': '\u66fe\u90fd\u533a', 'level': 3 },
          '1811': { 'id': 1811, 'pid': 1809, 'name': '\u968f\u53bf', 'level': 3 },
          '1812': { 'id': 1812, 'pid': 1809, 'name': '\u5e7f\u6c34\u5e02', 'level': 3 }
        }
      },
      '1813': {
        'id': 1813,
        'pid': 1709,
        'name': '\u6069\u65bd\u571f\u5bb6\u65cf\u82d7\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '1814': { 'id': 1814, 'pid': 1813, 'name': '\u6069\u65bd\u5e02', 'level': 3 },
          '1815': { 'id': 1815, 'pid': 1813, 'name': '\u5229\u5ddd\u5e02', 'level': 3 },
          '1816': { 'id': 1816, 'pid': 1813, 'name': '\u5efa\u59cb\u53bf', 'level': 3 },
          '1817': { 'id': 1817, 'pid': 1813, 'name': '\u5df4\u4e1c\u53bf', 'level': 3 },
          '1818': { 'id': 1818, 'pid': 1813, 'name': '\u5ba3\u6069\u53bf', 'level': 3 },
          '1819': { 'id': 1819, 'pid': 1813, 'name': '\u54b8\u4e30\u53bf', 'level': 3 },
          '1820': { 'id': 1820, 'pid': 1813, 'name': '\u6765\u51e4\u53bf', 'level': 3 },
          '1821': { 'id': 1821, 'pid': 1813, 'name': '\u9e64\u5cf0\u53bf', 'level': 3 }
        }
      },
      '1822': {
        'id': 1822,
        'pid': 1709,
        'name': '\u7701\u76f4\u8f96\u53bf\u7ea7\u884c\u653f\u533a\u5212',
        'level': 2,
        'region': {
          '1823': { 'id': 1823, 'pid': 1822, 'name': '\u4ed9\u6843\u5e02', 'level': 3 },
          '1824': { 'id': 1824, 'pid': 1822, 'name': '\u6f5c\u6c5f\u5e02', 'level': 3 },
          '1825': { 'id': 1825, 'pid': 1822, 'name': '\u5929\u95e8\u5e02', 'level': 3 },
          '1826': { 'id': 1826, 'pid': 1822, 'name': '\u795e\u519c\u67b6\u6797\u533a', 'level': 3 }
        }
      }
    }
  },
  '1827': {
    'id': 1827, 'pid': 0, 'name': '\u6e56\u5357\u7701', 'level': 1, 'city': {
      '1828': {
        'id': 1828,
        'pid': 1827,
        'name': '\u957f\u6c99\u5e02',
        'level': 2,
        'region': {
          '1829': { 'id': 1829, 'pid': 1828, 'name': '\u8299\u84c9\u533a', 'level': 3 },
          '1830': { 'id': 1830, 'pid': 1828, 'name': '\u5929\u5fc3\u533a', 'level': 3 },
          '1831': { 'id': 1831, 'pid': 1828, 'name': '\u5cb3\u9e93\u533a', 'level': 3 },
          '1832': { 'id': 1832, 'pid': 1828, 'name': '\u5f00\u798f\u533a', 'level': 3 },
          '1833': { 'id': 1833, 'pid': 1828, 'name': '\u96e8\u82b1\u533a', 'level': 3 },
          '1834': { 'id': 1834, 'pid': 1828, 'name': '\u671b\u57ce\u533a', 'level': 3 },
          '1835': { 'id': 1835, 'pid': 1828, 'name': '\u957f\u6c99\u53bf', 'level': 3 },
          '1836': { 'id': 1836, 'pid': 1828, 'name': '\u5b81\u4e61\u53bf', 'level': 3 },
          '1837': { 'id': 1837, 'pid': 1828, 'name': '\u6d4f\u9633\u5e02', 'level': 3 }
        }
      },
      '1838': {
        'id': 1838,
        'pid': 1827,
        'name': '\u682a\u6d32\u5e02',
        'level': 2,
        'region': {
          '1839': { 'id': 1839, 'pid': 1838, 'name': '\u8377\u5858\u533a', 'level': 3 },
          '1840': { 'id': 1840, 'pid': 1838, 'name': '\u82a6\u6dde\u533a', 'level': 3 },
          '1841': { 'id': 1841, 'pid': 1838, 'name': '\u77f3\u5cf0\u533a', 'level': 3 },
          '1842': { 'id': 1842, 'pid': 1838, 'name': '\u5929\u5143\u533a', 'level': 3 },
          '1843': { 'id': 1843, 'pid': 1838, 'name': '\u682a\u6d32\u53bf', 'level': 3 },
          '1844': { 'id': 1844, 'pid': 1838, 'name': '\u6538\u53bf', 'level': 3 },
          '1845': { 'id': 1845, 'pid': 1838, 'name': '\u8336\u9675\u53bf', 'level': 3 },
          '1846': { 'id': 1846, 'pid': 1838, 'name': '\u708e\u9675\u53bf', 'level': 3 },
          '1847': { 'id': 1847, 'pid': 1838, 'name': '\u91b4\u9675\u5e02', 'level': 3 }
        }
      },
      '1848': {
        'id': 1848,
        'pid': 1827,
        'name': '\u6e58\u6f6d\u5e02',
        'level': 2,
        'region': {
          '1849': { 'id': 1849, 'pid': 1848, 'name': '\u96e8\u6e56\u533a', 'level': 3 },
          '1850': { 'id': 1850, 'pid': 1848, 'name': '\u5cb3\u5858\u533a', 'level': 3 },
          '1851': { 'id': 1851, 'pid': 1848, 'name': '\u6e58\u6f6d\u53bf', 'level': 3 },
          '1852': { 'id': 1852, 'pid': 1848, 'name': '\u6e58\u4e61\u5e02', 'level': 3 },
          '1853': { 'id': 1853, 'pid': 1848, 'name': '\u97f6\u5c71\u5e02', 'level': 3 }
        }
      },
      '1854': {
        'id': 1854,
        'pid': 1827,
        'name': '\u8861\u9633\u5e02',
        'level': 2,
        'region': {
          '1855': { 'id': 1855, 'pid': 1854, 'name': '\u73e0\u6656\u533a', 'level': 3 },
          '1856': { 'id': 1856, 'pid': 1854, 'name': '\u96c1\u5cf0\u533a', 'level': 3 },
          '1857': { 'id': 1857, 'pid': 1854, 'name': '\u77f3\u9f13\u533a', 'level': 3 },
          '1858': { 'id': 1858, 'pid': 1854, 'name': '\u84b8\u6e58\u533a', 'level': 3 },
          '1859': { 'id': 1859, 'pid': 1854, 'name': '\u5357\u5cb3\u533a', 'level': 3 },
          '1860': { 'id': 1860, 'pid': 1854, 'name': '\u8861\u9633\u53bf', 'level': 3 },
          '1861': { 'id': 1861, 'pid': 1854, 'name': '\u8861\u5357\u53bf', 'level': 3 },
          '1862': { 'id': 1862, 'pid': 1854, 'name': '\u8861\u5c71\u53bf', 'level': 3 },
          '1863': { 'id': 1863, 'pid': 1854, 'name': '\u8861\u4e1c\u53bf', 'level': 3 },
          '1864': { 'id': 1864, 'pid': 1854, 'name': '\u7941\u4e1c\u53bf', 'level': 3 },
          '1865': { 'id': 1865, 'pid': 1854, 'name': '\u8012\u9633\u5e02', 'level': 3 },
          '1866': { 'id': 1866, 'pid': 1854, 'name': '\u5e38\u5b81\u5e02', 'level': 3 }
        }
      },
      '1867': {
        'id': 1867,
        'pid': 1827,
        'name': '\u90b5\u9633\u5e02',
        'level': 2,
        'region': {
          '1868': { 'id': 1868, 'pid': 1867, 'name': '\u53cc\u6e05\u533a', 'level': 3 },
          '1869': { 'id': 1869, 'pid': 1867, 'name': '\u5927\u7965\u533a', 'level': 3 },
          '1870': { 'id': 1870, 'pid': 1867, 'name': '\u5317\u5854\u533a', 'level': 3 },
          '1871': { 'id': 1871, 'pid': 1867, 'name': '\u90b5\u4e1c\u53bf', 'level': 3 },
          '1872': { 'id': 1872, 'pid': 1867, 'name': '\u65b0\u90b5\u53bf', 'level': 3 },
          '1873': { 'id': 1873, 'pid': 1867, 'name': '\u90b5\u9633\u53bf', 'level': 3 },
          '1874': { 'id': 1874, 'pid': 1867, 'name': '\u9686\u56de\u53bf', 'level': 3 },
          '1875': { 'id': 1875, 'pid': 1867, 'name': '\u6d1e\u53e3\u53bf', 'level': 3 },
          '1876': { 'id': 1876, 'pid': 1867, 'name': '\u7ee5\u5b81\u53bf', 'level': 3 },
          '1877': { 'id': 1877, 'pid': 1867, 'name': '\u65b0\u5b81\u53bf', 'level': 3 },
          '1878': { 'id': 1878, 'pid': 1867, 'name': '\u57ce\u6b65\u82d7\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '1879': { 'id': 1879, 'pid': 1867, 'name': '\u6b66\u5188\u5e02', 'level': 3 }
        }
      },
      '1880': {
        'id': 1880,
        'pid': 1827,
        'name': '\u5cb3\u9633\u5e02',
        'level': 2,
        'region': {
          '1881': { 'id': 1881, 'pid': 1880, 'name': '\u5cb3\u9633\u697c\u533a', 'level': 3 },
          '1882': { 'id': 1882, 'pid': 1880, 'name': '\u4e91\u6eaa\u533a', 'level': 3 },
          '1883': { 'id': 1883, 'pid': 1880, 'name': '\u541b\u5c71\u533a', 'level': 3 },
          '1884': { 'id': 1884, 'pid': 1880, 'name': '\u5cb3\u9633\u53bf', 'level': 3 },
          '1885': { 'id': 1885, 'pid': 1880, 'name': '\u534e\u5bb9\u53bf', 'level': 3 },
          '1886': { 'id': 1886, 'pid': 1880, 'name': '\u6e58\u9634\u53bf', 'level': 3 },
          '1887': { 'id': 1887, 'pid': 1880, 'name': '\u5e73\u6c5f\u53bf', 'level': 3 },
          '1888': { 'id': 1888, 'pid': 1880, 'name': '\u6c68\u7f57\u5e02', 'level': 3 },
          '1889': { 'id': 1889, 'pid': 1880, 'name': '\u4e34\u6e58\u5e02', 'level': 3 }
        }
      },
      '1890': {
        'id': 1890,
        'pid': 1827,
        'name': '\u5e38\u5fb7\u5e02',
        'level': 2,
        'region': {
          '1891': { 'id': 1891, 'pid': 1890, 'name': '\u6b66\u9675\u533a', 'level': 3 },
          '1892': { 'id': 1892, 'pid': 1890, 'name': '\u9f0e\u57ce\u533a', 'level': 3 },
          '1893': { 'id': 1893, 'pid': 1890, 'name': '\u5b89\u4e61\u53bf', 'level': 3 },
          '1894': { 'id': 1894, 'pid': 1890, 'name': '\u6c49\u5bff\u53bf', 'level': 3 },
          '1895': { 'id': 1895, 'pid': 1890, 'name': '\u6fa7\u53bf', 'level': 3 },
          '1896': { 'id': 1896, 'pid': 1890, 'name': '\u4e34\u6fa7\u53bf', 'level': 3 },
          '1897': { 'id': 1897, 'pid': 1890, 'name': '\u6843\u6e90\u53bf', 'level': 3 },
          '1898': { 'id': 1898, 'pid': 1890, 'name': '\u77f3\u95e8\u53bf', 'level': 3 },
          '1899': { 'id': 1899, 'pid': 1890, 'name': '\u6d25\u5e02\u5e02', 'level': 3 }
        }
      },
      '1900': {
        'id': 1900,
        'pid': 1827,
        'name': '\u5f20\u5bb6\u754c\u5e02',
        'level': 2,
        'region': {
          '1901': { 'id': 1901, 'pid': 1900, 'name': '\u6c38\u5b9a\u533a', 'level': 3 },
          '1902': { 'id': 1902, 'pid': 1900, 'name': '\u6b66\u9675\u6e90\u533a', 'level': 3 },
          '1903': { 'id': 1903, 'pid': 1900, 'name': '\u6148\u5229\u53bf', 'level': 3 },
          '1904': { 'id': 1904, 'pid': 1900, 'name': '\u6851\u690d\u53bf', 'level': 3 }
        }
      },
      '1905': {
        'id': 1905,
        'pid': 1827,
        'name': '\u76ca\u9633\u5e02',
        'level': 2,
        'region': {
          '1906': { 'id': 1906, 'pid': 1905, 'name': '\u8d44\u9633\u533a', 'level': 3 },
          '1907': { 'id': 1907, 'pid': 1905, 'name': '\u8d6b\u5c71\u533a', 'level': 3 },
          '1908': { 'id': 1908, 'pid': 1905, 'name': '\u5357\u53bf', 'level': 3 },
          '1909': { 'id': 1909, 'pid': 1905, 'name': '\u6843\u6c5f\u53bf', 'level': 3 },
          '1910': { 'id': 1910, 'pid': 1905, 'name': '\u5b89\u5316\u53bf', 'level': 3 },
          '1911': { 'id': 1911, 'pid': 1905, 'name': '\u6c85\u6c5f\u5e02', 'level': 3 }
        }
      },
      '1912': {
        'id': 1912,
        'pid': 1827,
        'name': '\u90f4\u5dde\u5e02',
        'level': 2,
        'region': {
          '1913': { 'id': 1913, 'pid': 1912, 'name': '\u5317\u6e56\u533a', 'level': 3 },
          '1914': { 'id': 1914, 'pid': 1912, 'name': '\u82cf\u4ed9\u533a', 'level': 3 },
          '1915': { 'id': 1915, 'pid': 1912, 'name': '\u6842\u9633\u53bf', 'level': 3 },
          '1916': { 'id': 1916, 'pid': 1912, 'name': '\u5b9c\u7ae0\u53bf', 'level': 3 },
          '1917': { 'id': 1917, 'pid': 1912, 'name': '\u6c38\u5174\u53bf', 'level': 3 },
          '1918': { 'id': 1918, 'pid': 1912, 'name': '\u5609\u79be\u53bf', 'level': 3 },
          '1919': { 'id': 1919, 'pid': 1912, 'name': '\u4e34\u6b66\u53bf', 'level': 3 },
          '1920': { 'id': 1920, 'pid': 1912, 'name': '\u6c5d\u57ce\u53bf', 'level': 3 },
          '1921': { 'id': 1921, 'pid': 1912, 'name': '\u6842\u4e1c\u53bf', 'level': 3 },
          '1922': { 'id': 1922, 'pid': 1912, 'name': '\u5b89\u4ec1\u53bf', 'level': 3 },
          '1923': { 'id': 1923, 'pid': 1912, 'name': '\u8d44\u5174\u5e02', 'level': 3 }
        }
      },
      '1924': {
        'id': 1924,
        'pid': 1827,
        'name': '\u6c38\u5dde\u5e02',
        'level': 2,
        'region': {
          '1925': { 'id': 1925, 'pid': 1924, 'name': '\u96f6\u9675\u533a', 'level': 3 },
          '1926': { 'id': 1926, 'pid': 1924, 'name': '\u51b7\u6c34\u6ee9\u533a', 'level': 3 },
          '1927': { 'id': 1927, 'pid': 1924, 'name': '\u7941\u9633\u53bf', 'level': 3 },
          '1928': { 'id': 1928, 'pid': 1924, 'name': '\u4e1c\u5b89\u53bf', 'level': 3 },
          '1929': { 'id': 1929, 'pid': 1924, 'name': '\u53cc\u724c\u53bf', 'level': 3 },
          '1930': { 'id': 1930, 'pid': 1924, 'name': '\u9053\u53bf', 'level': 3 },
          '1931': { 'id': 1931, 'pid': 1924, 'name': '\u6c5f\u6c38\u53bf', 'level': 3 },
          '1932': { 'id': 1932, 'pid': 1924, 'name': '\u5b81\u8fdc\u53bf', 'level': 3 },
          '1933': { 'id': 1933, 'pid': 1924, 'name': '\u84dd\u5c71\u53bf', 'level': 3 },
          '1934': { 'id': 1934, 'pid': 1924, 'name': '\u65b0\u7530\u53bf', 'level': 3 },
          '1935': { 'id': 1935, 'pid': 1924, 'name': '\u6c5f\u534e\u7476\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '1936': {
        'id': 1936,
        'pid': 1827,
        'name': '\u6000\u5316\u5e02',
        'level': 2,
        'region': {
          '1937': { 'id': 1937, 'pid': 1936, 'name': '\u9e64\u57ce\u533a', 'level': 3 },
          '1938': { 'id': 1938, 'pid': 1936, 'name': '\u4e2d\u65b9\u53bf', 'level': 3 },
          '1939': { 'id': 1939, 'pid': 1936, 'name': '\u6c85\u9675\u53bf', 'level': 3 },
          '1940': { 'id': 1940, 'pid': 1936, 'name': '\u8fb0\u6eaa\u53bf', 'level': 3 },
          '1941': { 'id': 1941, 'pid': 1936, 'name': '\u6e86\u6d66\u53bf', 'level': 3 },
          '1942': { 'id': 1942, 'pid': 1936, 'name': '\u4f1a\u540c\u53bf', 'level': 3 },
          '1943': { 'id': 1943, 'pid': 1936, 'name': '\u9ebb\u9633\u82d7\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '1944': { 'id': 1944, 'pid': 1936, 'name': '\u65b0\u6643\u4f97\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '1945': { 'id': 1945, 'pid': 1936, 'name': '\u82b7\u6c5f\u4f97\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '1946': {
            'id': 1946,
            'pid': 1936,
            'name': '\u9756\u5dde\u82d7\u65cf\u4f97\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '1947': { 'id': 1947, 'pid': 1936, 'name': '\u901a\u9053\u4f97\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '1948': { 'id': 1948, 'pid': 1936, 'name': '\u6d2a\u6c5f\u5e02', 'level': 3 }
        }
      },
      '1949': {
        'id': 1949,
        'pid': 1827,
        'name': '\u5a04\u5e95\u5e02',
        'level': 2,
        'region': {
          '1950': { 'id': 1950, 'pid': 1949, 'name': '\u5a04\u661f\u533a', 'level': 3 },
          '1951': { 'id': 1951, 'pid': 1949, 'name': '\u53cc\u5cf0\u53bf', 'level': 3 },
          '1952': { 'id': 1952, 'pid': 1949, 'name': '\u65b0\u5316\u53bf', 'level': 3 },
          '1953': { 'id': 1953, 'pid': 1949, 'name': '\u51b7\u6c34\u6c5f\u5e02', 'level': 3 },
          '1954': { 'id': 1954, 'pid': 1949, 'name': '\u6d9f\u6e90\u5e02', 'level': 3 }
        }
      },
      '1955': {
        'id': 1955,
        'pid': 1827,
        'name': '\u6e58\u897f\u571f\u5bb6\u65cf\u82d7\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '1956': { 'id': 1956, 'pid': 1955, 'name': '\u5409\u9996\u5e02', 'level': 3 },
          '1957': { 'id': 1957, 'pid': 1955, 'name': '\u6cf8\u6eaa\u53bf', 'level': 3 },
          '1958': { 'id': 1958, 'pid': 1955, 'name': '\u51e4\u51f0\u53bf', 'level': 3 },
          '1959': { 'id': 1959, 'pid': 1955, 'name': '\u82b1\u57a3\u53bf', 'level': 3 },
          '1960': { 'id': 1960, 'pid': 1955, 'name': '\u4fdd\u9756\u53bf', 'level': 3 },
          '1961': { 'id': 1961, 'pid': 1955, 'name': '\u53e4\u4e08\u53bf', 'level': 3 },
          '1962': { 'id': 1962, 'pid': 1955, 'name': '\u6c38\u987a\u53bf', 'level': 3 },
          '1963': { 'id': 1963, 'pid': 1955, 'name': '\u9f99\u5c71\u53bf', 'level': 3 }
        }
      }
    }
  },
  '1964': {
    'id': 1964, 'pid': 0, 'name': '\u5e7f\u4e1c\u7701', 'level': 1, 'city': {
      '1965': {
        'id': 1965,
        'pid': 1964,
        'name': '\u5e7f\u5dde\u5e02',
        'level': 2,
        'region': {
          '1966': { 'id': 1966, 'pid': 1965, 'name': '\u8354\u6e7e\u533a', 'level': 3 },
          '1967': { 'id': 1967, 'pid': 1965, 'name': '\u8d8a\u79c0\u533a', 'level': 3 },
          '1968': { 'id': 1968, 'pid': 1965, 'name': '\u6d77\u73e0\u533a', 'level': 3 },
          '1969': { 'id': 1969, 'pid': 1965, 'name': '\u5929\u6cb3\u533a', 'level': 3 },
          '1970': { 'id': 1970, 'pid': 1965, 'name': '\u767d\u4e91\u533a', 'level': 3 },
          '1971': { 'id': 1971, 'pid': 1965, 'name': '\u9ec4\u57d4\u533a', 'level': 3 },
          '1972': { 'id': 1972, 'pid': 1965, 'name': '\u756a\u79ba\u533a', 'level': 3 },
          '1973': { 'id': 1973, 'pid': 1965, 'name': '\u82b1\u90fd\u533a', 'level': 3 },
          '1974': { 'id': 1974, 'pid': 1965, 'name': '\u5357\u6c99\u533a', 'level': 3 },
          '1975': { 'id': 1975, 'pid': 1965, 'name': '\u4ece\u5316\u533a', 'level': 3 },
          '1976': { 'id': 1976, 'pid': 1965, 'name': '\u589e\u57ce\u533a', 'level': 3 }
        }
      },
      '1977': {
        'id': 1977,
        'pid': 1964,
        'name': '\u97f6\u5173\u5e02',
        'level': 2,
        'region': {
          '1978': { 'id': 1978, 'pid': 1977, 'name': '\u6b66\u6c5f\u533a', 'level': 3 },
          '1979': { 'id': 1979, 'pid': 1977, 'name': '\u6d48\u6c5f\u533a', 'level': 3 },
          '1980': { 'id': 1980, 'pid': 1977, 'name': '\u66f2\u6c5f\u533a', 'level': 3 },
          '1981': { 'id': 1981, 'pid': 1977, 'name': '\u59cb\u5174\u53bf', 'level': 3 },
          '1982': { 'id': 1982, 'pid': 1977, 'name': '\u4ec1\u5316\u53bf', 'level': 3 },
          '1983': { 'id': 1983, 'pid': 1977, 'name': '\u7fc1\u6e90\u53bf', 'level': 3 },
          '1984': { 'id': 1984, 'pid': 1977, 'name': '\u4e73\u6e90\u7476\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '1985': { 'id': 1985, 'pid': 1977, 'name': '\u65b0\u4e30\u53bf', 'level': 3 },
          '1986': { 'id': 1986, 'pid': 1977, 'name': '\u4e50\u660c\u5e02', 'level': 3 },
          '1987': { 'id': 1987, 'pid': 1977, 'name': '\u5357\u96c4\u5e02', 'level': 3 }
        }
      },
      '1988': {
        'id': 1988,
        'pid': 1964,
        'name': '\u6df1\u5733\u5e02',
        'level': 2,
        'region': {
          '1989': { 'id': 1989, 'pid': 1988, 'name': '\u7f57\u6e56\u533a', 'level': 3 },
          '1990': { 'id': 1990, 'pid': 1988, 'name': '\u798f\u7530\u533a', 'level': 3 },
          '1991': { 'id': 1991, 'pid': 1988, 'name': '\u5357\u5c71\u533a', 'level': 3 },
          '1992': { 'id': 1992, 'pid': 1988, 'name': '\u5b9d\u5b89\u533a', 'level': 3 },
          '1993': { 'id': 1993, 'pid': 1988, 'name': '\u9f99\u5c97\u533a', 'level': 3 },
          '1994': { 'id': 1994, 'pid': 1988, 'name': '\u76d0\u7530\u533a', 'level': 3 },
          '1995': { 'id': 1995, 'pid': 1988, 'name': '\u5149\u660e\u65b0\u533a', 'level': 3 },
          '1996': { 'id': 1996, 'pid': 1988, 'name': '\u576a\u5c71\u65b0\u533a', 'level': 3 },
          '1997': { 'id': 1997, 'pid': 1988, 'name': '\u5927\u9e4f\u65b0\u533a', 'level': 3 },
          '1998': { 'id': 1998, 'pid': 1988, 'name': '\u9f99\u534e\u65b0\u533a', 'level': 3 }
        }
      },
      '1999': {
        'id': 1999,
        'pid': 1964,
        'name': '\u73e0\u6d77\u5e02',
        'level': 2,
        'region': {
          '2000': { 'id': 2000, 'pid': 1999, 'name': '\u9999\u6d32\u533a', 'level': 3 },
          '2001': { 'id': 2001, 'pid': 1999, 'name': '\u6597\u95e8\u533a', 'level': 3 },
          '2002': { 'id': 2002, 'pid': 1999, 'name': '\u91d1\u6e7e\u533a', 'level': 3 }
        }
      },
      '2003': {
        'id': 2003,
        'pid': 1964,
        'name': '\u6c55\u5934\u5e02',
        'level': 2,
        'region': {
          '2004': { 'id': 2004, 'pid': 2003, 'name': '\u9f99\u6e56\u533a', 'level': 3 },
          '2005': { 'id': 2005, 'pid': 2003, 'name': '\u91d1\u5e73\u533a', 'level': 3 },
          '2006': { 'id': 2006, 'pid': 2003, 'name': '\u6fe0\u6c5f\u533a', 'level': 3 },
          '2007': { 'id': 2007, 'pid': 2003, 'name': '\u6f6e\u9633\u533a', 'level': 3 },
          '2008': { 'id': 2008, 'pid': 2003, 'name': '\u6f6e\u5357\u533a', 'level': 3 },
          '2009': { 'id': 2009, 'pid': 2003, 'name': '\u6f84\u6d77\u533a', 'level': 3 },
          '2010': { 'id': 2010, 'pid': 2003, 'name': '\u5357\u6fb3\u53bf', 'level': 3 }
        }
      },
      '2011': {
        'id': 2011,
        'pid': 1964,
        'name': '\u4f5b\u5c71\u5e02',
        'level': 2,
        'region': {
          '2012': { 'id': 2012, 'pid': 2011, 'name': '\u7985\u57ce\u533a', 'level': 3 },
          '2013': { 'id': 2013, 'pid': 2011, 'name': '\u5357\u6d77\u533a', 'level': 3 },
          '2014': { 'id': 2014, 'pid': 2011, 'name': '\u987a\u5fb7\u533a', 'level': 3 },
          '2015': { 'id': 2015, 'pid': 2011, 'name': '\u4e09\u6c34\u533a', 'level': 3 },
          '2016': { 'id': 2016, 'pid': 2011, 'name': '\u9ad8\u660e\u533a', 'level': 3 }
        }
      },
      '2017': {
        'id': 2017,
        'pid': 1964,
        'name': '\u6c5f\u95e8\u5e02',
        'level': 2,
        'region': {
          '2018': { 'id': 2018, 'pid': 2017, 'name': '\u84ec\u6c5f\u533a', 'level': 3 },
          '2019': { 'id': 2019, 'pid': 2017, 'name': '\u6c5f\u6d77\u533a', 'level': 3 },
          '2020': { 'id': 2020, 'pid': 2017, 'name': '\u65b0\u4f1a\u533a', 'level': 3 },
          '2021': { 'id': 2021, 'pid': 2017, 'name': '\u53f0\u5c71\u5e02', 'level': 3 },
          '2022': { 'id': 2022, 'pid': 2017, 'name': '\u5f00\u5e73\u5e02', 'level': 3 },
          '2023': { 'id': 2023, 'pid': 2017, 'name': '\u9e64\u5c71\u5e02', 'level': 3 },
          '2024': { 'id': 2024, 'pid': 2017, 'name': '\u6069\u5e73\u5e02', 'level': 3 }
        }
      },
      '2025': {
        'id': 2025,
        'pid': 1964,
        'name': '\u6e5b\u6c5f\u5e02',
        'level': 2,
        'region': {
          '2026': { 'id': 2026, 'pid': 2025, 'name': '\u8d64\u574e\u533a', 'level': 3 },
          '2027': { 'id': 2027, 'pid': 2025, 'name': '\u971e\u5c71\u533a', 'level': 3 },
          '2028': { 'id': 2028, 'pid': 2025, 'name': '\u5761\u5934\u533a', 'level': 3 },
          '2029': { 'id': 2029, 'pid': 2025, 'name': '\u9ebb\u7ae0\u533a', 'level': 3 },
          '2030': { 'id': 2030, 'pid': 2025, 'name': '\u9042\u6eaa\u53bf', 'level': 3 },
          '2031': { 'id': 2031, 'pid': 2025, 'name': '\u5f90\u95fb\u53bf', 'level': 3 },
          '2032': { 'id': 2032, 'pid': 2025, 'name': '\u5ec9\u6c5f\u5e02', 'level': 3 },
          '2033': { 'id': 2033, 'pid': 2025, 'name': '\u96f7\u5dde\u5e02', 'level': 3 },
          '2034': { 'id': 2034, 'pid': 2025, 'name': '\u5434\u5ddd\u5e02', 'level': 3 }
        }
      },
      '2035': {
        'id': 2035,
        'pid': 1964,
        'name': '\u8302\u540d\u5e02',
        'level': 2,
        'region': {
          '2036': { 'id': 2036, 'pid': 2035, 'name': '\u8302\u5357\u533a', 'level': 3 },
          '2037': { 'id': 2037, 'pid': 2035, 'name': '\u7535\u767d\u533a', 'level': 3 },
          '2038': { 'id': 2038, 'pid': 2035, 'name': '\u9ad8\u5dde\u5e02', 'level': 3 },
          '2039': { 'id': 2039, 'pid': 2035, 'name': '\u5316\u5dde\u5e02', 'level': 3 },
          '2040': { 'id': 2040, 'pid': 2035, 'name': '\u4fe1\u5b9c\u5e02', 'level': 3 }
        }
      },
      '2041': {
        'id': 2041,
        'pid': 1964,
        'name': '\u8087\u5e86\u5e02',
        'level': 2,
        'region': {
          '2042': { 'id': 2042, 'pid': 2041, 'name': '\u7aef\u5dde\u533a', 'level': 3 },
          '2043': { 'id': 2043, 'pid': 2041, 'name': '\u9f0e\u6e56\u533a', 'level': 3 },
          '2044': { 'id': 2044, 'pid': 2041, 'name': '\u5e7f\u5b81\u53bf', 'level': 3 },
          '2045': { 'id': 2045, 'pid': 2041, 'name': '\u6000\u96c6\u53bf', 'level': 3 },
          '2046': { 'id': 2046, 'pid': 2041, 'name': '\u5c01\u5f00\u53bf', 'level': 3 },
          '2047': { 'id': 2047, 'pid': 2041, 'name': '\u5fb7\u5e86\u53bf', 'level': 3 },
          '2048': { 'id': 2048, 'pid': 2041, 'name': '\u9ad8\u8981\u5e02', 'level': 3 },
          '2049': { 'id': 2049, 'pid': 2041, 'name': '\u56db\u4f1a\u5e02', 'level': 3 }
        }
      },
      '2050': {
        'id': 2050,
        'pid': 1964,
        'name': '\u60e0\u5dde\u5e02',
        'level': 2,
        'region': {
          '2051': { 'id': 2051, 'pid': 2050, 'name': '\u60e0\u57ce\u533a', 'level': 3 },
          '2052': { 'id': 2052, 'pid': 2050, 'name': '\u60e0\u9633\u533a', 'level': 3 },
          '2053': { 'id': 2053, 'pid': 2050, 'name': '\u535a\u7f57\u53bf', 'level': 3 },
          '2054': { 'id': 2054, 'pid': 2050, 'name': '\u60e0\u4e1c\u53bf', 'level': 3 },
          '2055': { 'id': 2055, 'pid': 2050, 'name': '\u9f99\u95e8\u53bf', 'level': 3 }
        }
      },
      '2056': {
        'id': 2056,
        'pid': 1964,
        'name': '\u6885\u5dde\u5e02',
        'level': 2,
        'region': {
          '2057': { 'id': 2057, 'pid': 2056, 'name': '\u6885\u6c5f\u533a', 'level': 3 },
          '2058': { 'id': 2058, 'pid': 2056, 'name': '\u6885\u53bf\u533a', 'level': 3 },
          '2059': { 'id': 2059, 'pid': 2056, 'name': '\u5927\u57d4\u53bf', 'level': 3 },
          '2060': { 'id': 2060, 'pid': 2056, 'name': '\u4e30\u987a\u53bf', 'level': 3 },
          '2061': { 'id': 2061, 'pid': 2056, 'name': '\u4e94\u534e\u53bf', 'level': 3 },
          '2062': { 'id': 2062, 'pid': 2056, 'name': '\u5e73\u8fdc\u53bf', 'level': 3 },
          '2063': { 'id': 2063, 'pid': 2056, 'name': '\u8549\u5cad\u53bf', 'level': 3 },
          '2064': { 'id': 2064, 'pid': 2056, 'name': '\u5174\u5b81\u5e02', 'level': 3 }
        }
      },
      '2065': {
        'id': 2065,
        'pid': 1964,
        'name': '\u6c55\u5c3e\u5e02',
        'level': 2,
        'region': {
          '2066': { 'id': 2066, 'pid': 2065, 'name': '\u57ce\u533a', 'level': 3 },
          '2067': { 'id': 2067, 'pid': 2065, 'name': '\u6d77\u4e30\u53bf', 'level': 3 },
          '2068': { 'id': 2068, 'pid': 2065, 'name': '\u9646\u6cb3\u53bf', 'level': 3 },
          '2069': { 'id': 2069, 'pid': 2065, 'name': '\u9646\u4e30\u5e02', 'level': 3 }
        }
      },
      '2070': {
        'id': 2070,
        'pid': 1964,
        'name': '\u6cb3\u6e90\u5e02',
        'level': 2,
        'region': {
          '2071': { 'id': 2071, 'pid': 2070, 'name': '\u6e90\u57ce\u533a', 'level': 3 },
          '2072': { 'id': 2072, 'pid': 2070, 'name': '\u7d2b\u91d1\u53bf', 'level': 3 },
          '2073': { 'id': 2073, 'pid': 2070, 'name': '\u9f99\u5ddd\u53bf', 'level': 3 },
          '2074': { 'id': 2074, 'pid': 2070, 'name': '\u8fde\u5e73\u53bf', 'level': 3 },
          '2075': { 'id': 2075, 'pid': 2070, 'name': '\u548c\u5e73\u53bf', 'level': 3 },
          '2076': { 'id': 2076, 'pid': 2070, 'name': '\u4e1c\u6e90\u53bf', 'level': 3 }
        }
      },
      '2077': {
        'id': 2077,
        'pid': 1964,
        'name': '\u9633\u6c5f\u5e02',
        'level': 2,
        'region': {
          '2078': { 'id': 2078, 'pid': 2077, 'name': '\u6c5f\u57ce\u533a', 'level': 3 },
          '2079': { 'id': 2079, 'pid': 2077, 'name': '\u9633\u4e1c\u533a', 'level': 3 },
          '2080': { 'id': 2080, 'pid': 2077, 'name': '\u9633\u897f\u53bf', 'level': 3 },
          '2081': { 'id': 2081, 'pid': 2077, 'name': '\u9633\u6625\u5e02', 'level': 3 }
        }
      },
      '2082': {
        'id': 2082,
        'pid': 1964,
        'name': '\u6e05\u8fdc\u5e02',
        'level': 2,
        'region': {
          '2083': { 'id': 2083, 'pid': 2082, 'name': '\u6e05\u57ce\u533a', 'level': 3 },
          '2084': { 'id': 2084, 'pid': 2082, 'name': '\u6e05\u65b0\u533a', 'level': 3 },
          '2085': { 'id': 2085, 'pid': 2082, 'name': '\u4f5b\u5188\u53bf', 'level': 3 },
          '2086': { 'id': 2086, 'pid': 2082, 'name': '\u9633\u5c71\u53bf', 'level': 3 },
          '2087': {
            'id': 2087,
            'pid': 2082,
            'name': '\u8fde\u5c71\u58ee\u65cf\u7476\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2088': { 'id': 2088, 'pid': 2082, 'name': '\u8fde\u5357\u7476\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2089': { 'id': 2089, 'pid': 2082, 'name': '\u82f1\u5fb7\u5e02', 'level': 3 },
          '2090': { 'id': 2090, 'pid': 2082, 'name': '\u8fde\u5dde\u5e02', 'level': 3 }
        }
      },
      '2091': {
        'id': 2091, 'pid': 1964, 'name': '\u4e1c\u839e\u5e02', 'level': 2, 'region': {
          '2092': { 'id': 2092, 'pid': 2091, 'name': '\u839e\u57ce\u533a', 'level': 3 },
          '2093': { 'id': 2093, 'pid': 2091, 'name': '\u5357\u57ce\u533a', 'level': 3 },
          '2094': { 'id': 2094, 'pid': 2091, 'name': '\u4e07\u6c5f\u533a', 'level': 3 },
          '2095': { 'id': 2095, 'pid': 2091, 'name': '\u77f3\u78a3\u9547', 'level': 3 },
          '2096': { 'id': 2096, 'pid': 2091, 'name': '\u77f3\u9f99\u9547', 'level': 3 },
          '2097': { 'id': 2097, 'pid': 2091, 'name': '\u8336\u5c71\u9547', 'level': 3 },
          '2098': { 'id': 2098, 'pid': 2091, 'name': '\u77f3\u6392\u9547', 'level': 3 },
          '2099': { 'id': 2099, 'pid': 2091, 'name': '\u4f01\u77f3\u9547', 'level': 3 },
          '2100': { 'id': 2100, 'pid': 2091, 'name': '\u6a2a\u6ca5\u9547', 'level': 3 },
          '2101': { 'id': 2101, 'pid': 2091, 'name': '\u6865\u5934\u9547', 'level': 3 },
          '2102': { 'id': 2102, 'pid': 2091, 'name': '\u8c22\u5c97\u9547', 'level': 3 },
          '2103': { 'id': 2103, 'pid': 2091, 'name': '\u4e1c\u5751\u9547', 'level': 3 },
          '2104': { 'id': 2104, 'pid': 2091, 'name': '\u5e38\u5e73\u9547', 'level': 3 },
          '2105': { 'id': 2105, 'pid': 2091, 'name': '\u5bee\u6b65\u9547', 'level': 3 },
          '2106': { 'id': 2106, 'pid': 2091, 'name': '\u5927\u6717\u9547', 'level': 3 },
          '2107': { 'id': 2107, 'pid': 2091, 'name': '\u9ebb\u6d8c\u9547', 'level': 3 },
          '2108': { 'id': 2108, 'pid': 2091, 'name': '\u4e2d\u5802\u9547', 'level': 3 },
          '2109': { 'id': 2109, 'pid': 2091, 'name': '\u9ad8\u57d7\u9547', 'level': 3 },
          '2110': { 'id': 2110, 'pid': 2091, 'name': '\u6a1f\u6728\u5934\u9547', 'level': 3 },
          '2111': { 'id': 2111, 'pid': 2091, 'name': '\u5927\u5cad\u5c71\u9547', 'level': 3 },
          '2112': { 'id': 2112, 'pid': 2091, 'name': '\u671b\u725b\u58a9\u9547', 'level': 3 },
          '2113': { 'id': 2113, 'pid': 2091, 'name': '\u9ec4\u6c5f\u9547', 'level': 3 },
          '2114': { 'id': 2114, 'pid': 2091, 'name': '\u6d2a\u6885\u9547', 'level': 3 },
          '2115': { 'id': 2115, 'pid': 2091, 'name': '\u6e05\u6eaa\u9547', 'level': 3 },
          '2116': { 'id': 2116, 'pid': 2091, 'name': '\u6c99\u7530\u9547', 'level': 3 },
          '2117': { 'id': 2117, 'pid': 2091, 'name': '\u9053\u6ed8\u9547', 'level': 3 },
          '2118': { 'id': 2118, 'pid': 2091, 'name': '\u5858\u53a6\u9547', 'level': 3 },
          '2119': { 'id': 2119, 'pid': 2091, 'name': '\u864e\u95e8\u9547', 'level': 3 },
          '2120': { 'id': 2120, 'pid': 2091, 'name': '\u539a\u8857\u9547', 'level': 3 },
          '2121': { 'id': 2121, 'pid': 2091, 'name': '\u51e4\u5c97\u9547', 'level': 3 },
          '2122': { 'id': 2122, 'pid': 2091, 'name': '\u957f\u5b89\u9547', 'level': 3 }
        }
      },
      '2123': {
        'id': 2123, 'pid': 1964, 'name': '\u4e2d\u5c71\u5e02', 'level': 2, 'region': {
          '2124': { 'id': 2124, 'pid': 2123, 'name': '\u77f3\u5c90\u533a', 'level': 3 },
          '2125': { 'id': 2125, 'pid': 2123, 'name': '\u5357\u533a', 'level': 3 },
          '2126': { 'id': 2126, 'pid': 2123, 'name': '\u4e94\u6842\u5c71\u533a', 'level': 3 },
          '2127': { 'id': 2127, 'pid': 2123, 'name': '\u706b\u70ac\u5f00\u53d1\u533a', 'level': 3 },
          '2128': { 'id': 2128, 'pid': 2123, 'name': '\u9ec4\u5703\u9547', 'level': 3 },
          '2129': { 'id': 2129, 'pid': 2123, 'name': '\u5357\u5934\u9547', 'level': 3 },
          '2130': { 'id': 2130, 'pid': 2123, 'name': '\u4e1c\u51e4\u9547', 'level': 3 },
          '2131': { 'id': 2131, 'pid': 2123, 'name': '\u961c\u6c99\u9547', 'level': 3 },
          '2132': { 'id': 2132, 'pid': 2123, 'name': '\u5c0f\u6984\u9547', 'level': 3 },
          '2133': { 'id': 2133, 'pid': 2123, 'name': '\u4e1c\u5347\u9547', 'level': 3 },
          '2134': { 'id': 2134, 'pid': 2123, 'name': '\u53e4\u9547\u9547', 'level': 3 },
          '2135': { 'id': 2135, 'pid': 2123, 'name': '\u6a2a\u680f\u9547', 'level': 3 },
          '2136': { 'id': 2136, 'pid': 2123, 'name': '\u4e09\u89d2\u9547', 'level': 3 },
          '2137': { 'id': 2137, 'pid': 2123, 'name': '\u6c11\u4f17\u9547', 'level': 3 },
          '2138': { 'id': 2138, 'pid': 2123, 'name': '\u5357\u6717\u9547', 'level': 3 },
          '2139': { 'id': 2139, 'pid': 2123, 'name': '\u6e2f\u53e3\u9547', 'level': 3 },
          '2140': { 'id': 2140, 'pid': 2123, 'name': '\u5927\u6d8c\u9547', 'level': 3 },
          '2141': { 'id': 2141, 'pid': 2123, 'name': '\u6c99\u6eaa\u9547', 'level': 3 },
          '2142': { 'id': 2142, 'pid': 2123, 'name': '\u4e09\u4e61\u9547', 'level': 3 },
          '2143': { 'id': 2143, 'pid': 2123, 'name': '\u677f\u8299\u9547', 'level': 3 },
          '2144': { 'id': 2144, 'pid': 2123, 'name': '\u795e\u6e7e\u9547', 'level': 3 },
          '2145': { 'id': 2145, 'pid': 2123, 'name': '\u5766\u6d32\u9547', 'level': 3 }
        }
      },
      '2146': {
        'id': 2146,
        'pid': 1964,
        'name': '\u6f6e\u5dde\u5e02',
        'level': 2,
        'region': {
          '2147': { 'id': 2147, 'pid': 2146, 'name': '\u6e58\u6865\u533a', 'level': 3 },
          '2148': { 'id': 2148, 'pid': 2146, 'name': '\u6f6e\u5b89\u533a', 'level': 3 },
          '2149': { 'id': 2149, 'pid': 2146, 'name': '\u9976\u5e73\u53bf', 'level': 3 }
        }
      },
      '2150': {
        'id': 2150,
        'pid': 1964,
        'name': '\u63ed\u9633\u5e02',
        'level': 2,
        'region': {
          '2151': { 'id': 2151, 'pid': 2150, 'name': '\u6995\u57ce\u533a', 'level': 3 },
          '2152': { 'id': 2152, 'pid': 2150, 'name': '\u63ed\u4e1c\u533a', 'level': 3 },
          '2153': { 'id': 2153, 'pid': 2150, 'name': '\u63ed\u897f\u53bf', 'level': 3 },
          '2154': { 'id': 2154, 'pid': 2150, 'name': '\u60e0\u6765\u53bf', 'level': 3 },
          '2155': { 'id': 2155, 'pid': 2150, 'name': '\u666e\u5b81\u5e02', 'level': 3 }
        }
      },
      '2156': {
        'id': 2156,
        'pid': 1964,
        'name': '\u4e91\u6d6e\u5e02',
        'level': 2,
        'region': {
          '2157': { 'id': 2157, 'pid': 2156, 'name': '\u4e91\u57ce\u533a', 'level': 3 },
          '2158': { 'id': 2158, 'pid': 2156, 'name': '\u4e91\u5b89\u533a', 'level': 3 },
          '2159': { 'id': 2159, 'pid': 2156, 'name': '\u65b0\u5174\u53bf', 'level': 3 },
          '2160': { 'id': 2160, 'pid': 2156, 'name': '\u90c1\u5357\u53bf', 'level': 3 },
          '2161': { 'id': 2161, 'pid': 2156, 'name': '\u7f57\u5b9a\u5e02', 'level': 3 }
        }
      }
    }
  },
  '2162': {
    'id': 2162, 'pid': 0, 'name': '\u5e7f\u897f\u58ee\u65cf\u81ea\u6cbb\u533a', 'level': 1, 'city': {
      '2163': {
        'id': 2163,
        'pid': 2162,
        'name': '\u5357\u5b81\u5e02',
        'level': 2,
        'region': {
          '2164': { 'id': 2164, 'pid': 2163, 'name': '\u5174\u5b81\u533a', 'level': 3 },
          '2165': { 'id': 2165, 'pid': 2163, 'name': '\u9752\u79c0\u533a', 'level': 3 },
          '2166': { 'id': 2166, 'pid': 2163, 'name': '\u6c5f\u5357\u533a', 'level': 3 },
          '2167': { 'id': 2167, 'pid': 2163, 'name': '\u897f\u4e61\u5858\u533a', 'level': 3 },
          '2168': { 'id': 2168, 'pid': 2163, 'name': '\u826f\u5e86\u533a', 'level': 3 },
          '2169': { 'id': 2169, 'pid': 2163, 'name': '\u9095\u5b81\u533a', 'level': 3 },
          '2170': { 'id': 2170, 'pid': 2163, 'name': '\u6b66\u9e23\u53bf', 'level': 3 },
          '2171': { 'id': 2171, 'pid': 2163, 'name': '\u9686\u5b89\u53bf', 'level': 3 },
          '2172': { 'id': 2172, 'pid': 2163, 'name': '\u9a6c\u5c71\u53bf', 'level': 3 },
          '2173': { 'id': 2173, 'pid': 2163, 'name': '\u4e0a\u6797\u53bf', 'level': 3 },
          '2174': { 'id': 2174, 'pid': 2163, 'name': '\u5bbe\u9633\u53bf', 'level': 3 },
          '2175': { 'id': 2175, 'pid': 2163, 'name': '\u6a2a\u53bf', 'level': 3 },
          '2176': { 'id': 2176, 'pid': 2163, 'name': '\u57cc\u4e1c\u65b0\u533a', 'level': 3 }
        }
      },
      '2177': {
        'id': 2177,
        'pid': 2162,
        'name': '\u67f3\u5dde\u5e02',
        'level': 2,
        'region': {
          '2178': { 'id': 2178, 'pid': 2177, 'name': '\u57ce\u4e2d\u533a', 'level': 3 },
          '2179': { 'id': 2179, 'pid': 2177, 'name': '\u9c7c\u5cf0\u533a', 'level': 3 },
          '2180': { 'id': 2180, 'pid': 2177, 'name': '\u67f3\u5357\u533a', 'level': 3 },
          '2181': { 'id': 2181, 'pid': 2177, 'name': '\u67f3\u5317\u533a', 'level': 3 },
          '2182': { 'id': 2182, 'pid': 2177, 'name': '\u67f3\u6c5f\u53bf', 'level': 3 },
          '2183': { 'id': 2183, 'pid': 2177, 'name': '\u67f3\u57ce\u53bf', 'level': 3 },
          '2184': { 'id': 2184, 'pid': 2177, 'name': '\u9e7f\u5be8\u53bf', 'level': 3 },
          '2185': { 'id': 2185, 'pid': 2177, 'name': '\u878d\u5b89\u53bf', 'level': 3 },
          '2186': { 'id': 2186, 'pid': 2177, 'name': '\u878d\u6c34\u82d7\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2187': { 'id': 2187, 'pid': 2177, 'name': '\u4e09\u6c5f\u4f97\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2188': { 'id': 2188, 'pid': 2177, 'name': '\u67f3\u4e1c\u65b0\u533a', 'level': 3 }
        }
      },
      '2189': {
        'id': 2189, 'pid': 2162, 'name': '\u6842\u6797\u5e02', 'level': 2, 'region': {
          '2190': { 'id': 2190, 'pid': 2189, 'name': '\u79c0\u5cf0\u533a', 'level': 3 },
          '2191': { 'id': 2191, 'pid': 2189, 'name': '\u53e0\u5f69\u533a', 'level': 3 },
          '2192': { 'id': 2192, 'pid': 2189, 'name': '\u8c61\u5c71\u533a', 'level': 3 },
          '2193': { 'id': 2193, 'pid': 2189, 'name': '\u4e03\u661f\u533a', 'level': 3 },
          '2194': { 'id': 2194, 'pid': 2189, 'name': '\u96c1\u5c71\u533a', 'level': 3 },
          '2195': { 'id': 2195, 'pid': 2189, 'name': '\u4e34\u6842\u533a', 'level': 3 },
          '2196': { 'id': 2196, 'pid': 2189, 'name': '\u9633\u6714\u53bf', 'level': 3 },
          '2197': { 'id': 2197, 'pid': 2189, 'name': '\u7075\u5ddd\u53bf', 'level': 3 },
          '2198': { 'id': 2198, 'pid': 2189, 'name': '\u5168\u5dde\u53bf', 'level': 3 },
          '2199': { 'id': 2199, 'pid': 2189, 'name': '\u5174\u5b89\u53bf', 'level': 3 },
          '2200': { 'id': 2200, 'pid': 2189, 'name': '\u6c38\u798f\u53bf', 'level': 3 },
          '2201': { 'id': 2201, 'pid': 2189, 'name': '\u704c\u9633\u53bf', 'level': 3 },
          '2202': { 'id': 2202, 'pid': 2189, 'name': '\u9f99\u80dc\u5404\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2203': { 'id': 2203, 'pid': 2189, 'name': '\u8d44\u6e90\u53bf', 'level': 3 },
          '2204': { 'id': 2204, 'pid': 2189, 'name': '\u5e73\u4e50\u53bf', 'level': 3 },
          '2205': { 'id': 2205, 'pid': 2189, 'name': '\u8354\u6d66\u53bf', 'level': 3 },
          '2206': { 'id': 2206, 'pid': 2189, 'name': '\u606d\u57ce\u7476\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '2207': {
        'id': 2207,
        'pid': 2162,
        'name': '\u68a7\u5dde\u5e02',
        'level': 2,
        'region': {
          '2208': { 'id': 2208, 'pid': 2207, 'name': '\u4e07\u79c0\u533a', 'level': 3 },
          '2209': { 'id': 2209, 'pid': 2207, 'name': '\u957f\u6d32\u533a', 'level': 3 },
          '2210': { 'id': 2210, 'pid': 2207, 'name': '\u9f99\u5729\u533a', 'level': 3 },
          '2211': { 'id': 2211, 'pid': 2207, 'name': '\u82cd\u68a7\u53bf', 'level': 3 },
          '2212': { 'id': 2212, 'pid': 2207, 'name': '\u85e4\u53bf', 'level': 3 },
          '2213': { 'id': 2213, 'pid': 2207, 'name': '\u8499\u5c71\u53bf', 'level': 3 },
          '2214': { 'id': 2214, 'pid': 2207, 'name': '\u5c91\u6eaa\u5e02', 'level': 3 }
        }
      },
      '2215': {
        'id': 2215,
        'pid': 2162,
        'name': '\u5317\u6d77\u5e02',
        'level': 2,
        'region': {
          '2216': { 'id': 2216, 'pid': 2215, 'name': '\u6d77\u57ce\u533a', 'level': 3 },
          '2217': { 'id': 2217, 'pid': 2215, 'name': '\u94f6\u6d77\u533a', 'level': 3 },
          '2218': { 'id': 2218, 'pid': 2215, 'name': '\u94c1\u5c71\u6e2f\u533a', 'level': 3 },
          '2219': { 'id': 2219, 'pid': 2215, 'name': '\u5408\u6d66\u53bf', 'level': 3 }
        }
      },
      '2220': {
        'id': 2220,
        'pid': 2162,
        'name': '\u9632\u57ce\u6e2f\u5e02',
        'level': 2,
        'region': {
          '2221': { 'id': 2221, 'pid': 2220, 'name': '\u6e2f\u53e3\u533a', 'level': 3 },
          '2222': { 'id': 2222, 'pid': 2220, 'name': '\u9632\u57ce\u533a', 'level': 3 },
          '2223': { 'id': 2223, 'pid': 2220, 'name': '\u4e0a\u601d\u53bf', 'level': 3 },
          '2224': { 'id': 2224, 'pid': 2220, 'name': '\u4e1c\u5174\u5e02', 'level': 3 }
        }
      },
      '2225': {
        'id': 2225,
        'pid': 2162,
        'name': '\u94a6\u5dde\u5e02',
        'level': 2,
        'region': {
          '2226': { 'id': 2226, 'pid': 2225, 'name': '\u94a6\u5357\u533a', 'level': 3 },
          '2227': { 'id': 2227, 'pid': 2225, 'name': '\u94a6\u5317\u533a', 'level': 3 },
          '2228': { 'id': 2228, 'pid': 2225, 'name': '\u7075\u5c71\u53bf', 'level': 3 },
          '2229': { 'id': 2229, 'pid': 2225, 'name': '\u6d66\u5317\u53bf', 'level': 3 }
        }
      },
      '2230': {
        'id': 2230,
        'pid': 2162,
        'name': '\u8d35\u6e2f\u5e02',
        'level': 2,
        'region': {
          '2231': { 'id': 2231, 'pid': 2230, 'name': '\u6e2f\u5317\u533a', 'level': 3 },
          '2232': { 'id': 2232, 'pid': 2230, 'name': '\u6e2f\u5357\u533a', 'level': 3 },
          '2233': { 'id': 2233, 'pid': 2230, 'name': '\u8983\u5858\u533a', 'level': 3 },
          '2234': { 'id': 2234, 'pid': 2230, 'name': '\u5e73\u5357\u53bf', 'level': 3 },
          '2235': { 'id': 2235, 'pid': 2230, 'name': '\u6842\u5e73\u5e02', 'level': 3 }
        }
      },
      '2236': {
        'id': 2236,
        'pid': 2162,
        'name': '\u7389\u6797\u5e02',
        'level': 2,
        'region': {
          '2237': { 'id': 2237, 'pid': 2236, 'name': '\u7389\u5dde\u533a', 'level': 3 },
          '2238': { 'id': 2238, 'pid': 2236, 'name': '\u798f\u7ef5\u533a', 'level': 3 },
          '2239': { 'id': 2239, 'pid': 2236, 'name': '\u7389\u4e1c\u65b0\u533a', 'level': 3 },
          '2240': { 'id': 2240, 'pid': 2236, 'name': '\u5bb9\u53bf', 'level': 3 },
          '2241': { 'id': 2241, 'pid': 2236, 'name': '\u9646\u5ddd\u53bf', 'level': 3 },
          '2242': { 'id': 2242, 'pid': 2236, 'name': '\u535a\u767d\u53bf', 'level': 3 },
          '2243': { 'id': 2243, 'pid': 2236, 'name': '\u5174\u4e1a\u53bf', 'level': 3 },
          '2244': { 'id': 2244, 'pid': 2236, 'name': '\u5317\u6d41\u5e02', 'level': 3 }
        }
      },
      '2245': {
        'id': 2245,
        'pid': 2162,
        'name': '\u767e\u8272\u5e02',
        'level': 2,
        'region': {
          '2246': { 'id': 2246, 'pid': 2245, 'name': '\u53f3\u6c5f\u533a', 'level': 3 },
          '2247': { 'id': 2247, 'pid': 2245, 'name': '\u7530\u9633\u53bf', 'level': 3 },
          '2248': { 'id': 2248, 'pid': 2245, 'name': '\u7530\u4e1c\u53bf', 'level': 3 },
          '2249': { 'id': 2249, 'pid': 2245, 'name': '\u5e73\u679c\u53bf', 'level': 3 },
          '2250': { 'id': 2250, 'pid': 2245, 'name': '\u5fb7\u4fdd\u53bf', 'level': 3 },
          '2251': { 'id': 2251, 'pid': 2245, 'name': '\u9756\u897f\u53bf', 'level': 3 },
          '2252': { 'id': 2252, 'pid': 2245, 'name': '\u90a3\u5761\u53bf', 'level': 3 },
          '2253': { 'id': 2253, 'pid': 2245, 'name': '\u51cc\u4e91\u53bf', 'level': 3 },
          '2254': { 'id': 2254, 'pid': 2245, 'name': '\u4e50\u4e1a\u53bf', 'level': 3 },
          '2255': { 'id': 2255, 'pid': 2245, 'name': '\u7530\u6797\u53bf', 'level': 3 },
          '2256': { 'id': 2256, 'pid': 2245, 'name': '\u897f\u6797\u53bf', 'level': 3 },
          '2257': { 'id': 2257, 'pid': 2245, 'name': '\u9686\u6797\u5404\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '2258': {
        'id': 2258,
        'pid': 2162,
        'name': '\u8d3a\u5dde\u5e02',
        'level': 2,
        'region': {
          '2259': { 'id': 2259, 'pid': 2258, 'name': '\u516b\u6b65\u533a', 'level': 3 },
          '2260': { 'id': 2260, 'pid': 2258, 'name': '\u662d\u5e73\u53bf', 'level': 3 },
          '2261': { 'id': 2261, 'pid': 2258, 'name': '\u949f\u5c71\u53bf', 'level': 3 },
          '2262': { 'id': 2262, 'pid': 2258, 'name': '\u5bcc\u5ddd\u7476\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2263': { 'id': 2263, 'pid': 2258, 'name': '\u5e73\u6842\u7ba1\u7406\u533a', 'level': 3 }
        }
      },
      '2264': {
        'id': 2264,
        'pid': 2162,
        'name': '\u6cb3\u6c60\u5e02',
        'level': 2,
        'region': {
          '2265': { 'id': 2265, 'pid': 2264, 'name': '\u91d1\u57ce\u6c5f\u533a', 'level': 3 },
          '2266': { 'id': 2266, 'pid': 2264, 'name': '\u5357\u4e39\u53bf', 'level': 3 },
          '2267': { 'id': 2267, 'pid': 2264, 'name': '\u5929\u5ce8\u53bf', 'level': 3 },
          '2268': { 'id': 2268, 'pid': 2264, 'name': '\u51e4\u5c71\u53bf', 'level': 3 },
          '2269': { 'id': 2269, 'pid': 2264, 'name': '\u4e1c\u5170\u53bf', 'level': 3 },
          '2270': { 'id': 2270, 'pid': 2264, 'name': '\u7f57\u57ce\u4eeb\u4f6c\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2271': { 'id': 2271, 'pid': 2264, 'name': '\u73af\u6c5f\u6bdb\u5357\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2272': { 'id': 2272, 'pid': 2264, 'name': '\u5df4\u9a6c\u7476\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2273': { 'id': 2273, 'pid': 2264, 'name': '\u90fd\u5b89\u7476\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2274': { 'id': 2274, 'pid': 2264, 'name': '\u5927\u5316\u7476\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2275': { 'id': 2275, 'pid': 2264, 'name': '\u5b9c\u5dde\u5e02', 'level': 3 }
        }
      },
      '2276': {
        'id': 2276,
        'pid': 2162,
        'name': '\u6765\u5bbe\u5e02',
        'level': 2,
        'region': {
          '2277': { 'id': 2277, 'pid': 2276, 'name': '\u5174\u5bbe\u533a', 'level': 3 },
          '2278': { 'id': 2278, 'pid': 2276, 'name': '\u5ffb\u57ce\u53bf', 'level': 3 },
          '2279': { 'id': 2279, 'pid': 2276, 'name': '\u8c61\u5dde\u53bf', 'level': 3 },
          '2280': { 'id': 2280, 'pid': 2276, 'name': '\u6b66\u5ba3\u53bf', 'level': 3 },
          '2281': { 'id': 2281, 'pid': 2276, 'name': '\u91d1\u79c0\u7476\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2282': { 'id': 2282, 'pid': 2276, 'name': '\u5408\u5c71\u5e02', 'level': 3 }
        }
      },
      '2283': {
        'id': 2283,
        'pid': 2162,
        'name': '\u5d07\u5de6\u5e02',
        'level': 2,
        'region': {
          '2284': { 'id': 2284, 'pid': 2283, 'name': '\u6c5f\u5dde\u533a', 'level': 3 },
          '2285': { 'id': 2285, 'pid': 2283, 'name': '\u6276\u7ee5\u53bf', 'level': 3 },
          '2286': { 'id': 2286, 'pid': 2283, 'name': '\u5b81\u660e\u53bf', 'level': 3 },
          '2287': { 'id': 2287, 'pid': 2283, 'name': '\u9f99\u5dde\u53bf', 'level': 3 },
          '2288': { 'id': 2288, 'pid': 2283, 'name': '\u5927\u65b0\u53bf', 'level': 3 },
          '2289': { 'id': 2289, 'pid': 2283, 'name': '\u5929\u7b49\u53bf', 'level': 3 },
          '2290': { 'id': 2290, 'pid': 2283, 'name': '\u51ed\u7965\u5e02', 'level': 3 }
        }
      }
    }
  },
  '2291': {
    'id': 2291, 'pid': 0, 'name': '\u6d77\u5357\u7701', 'level': 1, 'city': {
      '2292': {
        'id': 2292,
        'pid': 2291,
        'name': '\u6d77\u53e3\u5e02',
        'level': 2,
        'region': {
          '2293': { 'id': 2293, 'pid': 2292, 'name': '\u79c0\u82f1\u533a', 'level': 3 },
          '2294': { 'id': 2294, 'pid': 2292, 'name': '\u9f99\u534e\u533a', 'level': 3 },
          '2295': { 'id': 2295, 'pid': 2292, 'name': '\u743c\u5c71\u533a', 'level': 3 },
          '2296': { 'id': 2296, 'pid': 2292, 'name': '\u7f8e\u5170\u533a', 'level': 3 }
        }
      },
      '2297': {
        'id': 2297,
        'pid': 2291,
        'name': '\u4e09\u4e9a\u5e02',
        'level': 2,
        'region': {
          '2298': { 'id': 2298, 'pid': 2297, 'name': '\u6d77\u68e0\u533a', 'level': 3 },
          '2299': { 'id': 2299, 'pid': 2297, 'name': '\u5409\u9633\u533a', 'level': 3 },
          '2300': { 'id': 2300, 'pid': 2297, 'name': '\u5929\u6daf\u533a', 'level': 3 },
          '2301': { 'id': 2301, 'pid': 2297, 'name': '\u5d16\u5dde\u533a', 'level': 3 }
        }
      },
      '2302': {
        'id': 2302,
        'pid': 2291,
        'name': '\u4e09\u6c99\u5e02',
        'level': 2,
        'region': {
          '2303': { 'id': 2303, 'pid': 2302, 'name': '\u897f\u6c99\u7fa4\u5c9b', 'level': 3 },
          '2304': { 'id': 2304, 'pid': 2302, 'name': '\u5357\u6c99\u7fa4\u5c9b', 'level': 3 },
          '2305': { 'id': 2305, 'pid': 2302, 'name': '\u4e2d\u6c99\u7fa4\u5c9b', 'level': 3 }
        }
      },
      '2306': {
        'id': 2306, 'pid': 2291, 'name': '\u76f4\u8f96\u53bf\u7ea7', 'level': 2, 'region': {
          '2307': { 'id': 2307, 'pid': 2306, 'name': '\u4e94\u6307\u5c71\u5e02', 'level': 3 },
          '2308': { 'id': 2308, 'pid': 2306, 'name': '\u743c\u6d77\u5e02', 'level': 3 },
          '2309': { 'id': 2309, 'pid': 2306, 'name': '\u510b\u5dde\u5e02', 'level': 3 },
          '2310': { 'id': 2310, 'pid': 2306, 'name': '\u6587\u660c\u5e02', 'level': 3 },
          '2311': { 'id': 2311, 'pid': 2306, 'name': '\u4e07\u5b81\u5e02', 'level': 3 },
          '2312': { 'id': 2312, 'pid': 2306, 'name': '\u4e1c\u65b9\u5e02', 'level': 3 },
          '2313': { 'id': 2313, 'pid': 2306, 'name': '\u5b9a\u5b89\u53bf', 'level': 3 },
          '2314': { 'id': 2314, 'pid': 2306, 'name': '\u5c6f\u660c\u53bf', 'level': 3 },
          '2315': { 'id': 2315, 'pid': 2306, 'name': '\u6f84\u8fc8\u53bf', 'level': 3 },
          '2316': { 'id': 2316, 'pid': 2306, 'name': '\u4e34\u9ad8\u53bf', 'level': 3 },
          '2317': { 'id': 2317, 'pid': 2306, 'name': '\u767d\u6c99\u9ece\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2318': { 'id': 2318, 'pid': 2306, 'name': '\u660c\u6c5f\u9ece\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2319': { 'id': 2319, 'pid': 2306, 'name': '\u4e50\u4e1c\u9ece\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2320': { 'id': 2320, 'pid': 2306, 'name': '\u9675\u6c34\u9ece\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2321': {
            'id': 2321,
            'pid': 2306,
            'name': '\u4fdd\u4ead\u9ece\u65cf\u82d7\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2322': {
            'id': 2322,
            'pid': 2306,
            'name': '\u743c\u4e2d\u9ece\u65cf\u82d7\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          }
        }
      }
    }
  },
  '2323': {
    'id': 2323, 'pid': 0, 'name': '\u91cd\u5e86\u5e02', 'level': 1, 'city': {
      '2324': {
        'id': 2324, 'pid': 2323, 'name': '\u91cd\u5e86\u5e02', 'level': 2, 'region': {
          '2325': { 'id': 2325, 'pid': 2324, 'name': '\u4e07\u5dde\u533a', 'level': 3 },
          '2326': { 'id': 2326, 'pid': 2324, 'name': '\u6daa\u9675\u533a', 'level': 3 },
          '2327': { 'id': 2327, 'pid': 2324, 'name': '\u6e1d\u4e2d\u533a', 'level': 3 },
          '2328': { 'id': 2328, 'pid': 2324, 'name': '\u5927\u6e21\u53e3\u533a', 'level': 3 },
          '2329': { 'id': 2329, 'pid': 2324, 'name': '\u6c5f\u5317\u533a', 'level': 3 },
          '2330': { 'id': 2330, 'pid': 2324, 'name': '\u6c99\u576a\u575d\u533a', 'level': 3 },
          '2331': { 'id': 2331, 'pid': 2324, 'name': '\u4e5d\u9f99\u5761\u533a', 'level': 3 },
          '2332': { 'id': 2332, 'pid': 2324, 'name': '\u5357\u5cb8\u533a', 'level': 3 },
          '2333': { 'id': 2333, 'pid': 2324, 'name': '\u5317\u789a\u533a', 'level': 3 },
          '2334': { 'id': 2334, 'pid': 2324, 'name': '\u7da6\u6c5f\u533a', 'level': 3 },
          '2335': { 'id': 2335, 'pid': 2324, 'name': '\u5927\u8db3\u533a', 'level': 3 },
          '2336': { 'id': 2336, 'pid': 2324, 'name': '\u6e1d\u5317\u533a', 'level': 3 },
          '2337': { 'id': 2337, 'pid': 2324, 'name': '\u5df4\u5357\u533a', 'level': 3 },
          '2338': { 'id': 2338, 'pid': 2324, 'name': '\u9ed4\u6c5f\u533a', 'level': 3 },
          '2339': { 'id': 2339, 'pid': 2324, 'name': '\u957f\u5bff\u533a', 'level': 3 },
          '2340': { 'id': 2340, 'pid': 2324, 'name': '\u6c5f\u6d25\u533a', 'level': 3 },
          '2341': { 'id': 2341, 'pid': 2324, 'name': '\u5408\u5ddd\u533a', 'level': 3 },
          '2342': { 'id': 2342, 'pid': 2324, 'name': '\u6c38\u5ddd\u533a', 'level': 3 },
          '2343': { 'id': 2343, 'pid': 2324, 'name': '\u5357\u5ddd\u533a', 'level': 3 },
          '2344': { 'id': 2344, 'pid': 2324, 'name': '\u74a7\u5c71\u533a', 'level': 3 },
          '2345': { 'id': 2345, 'pid': 2324, 'name': '\u94dc\u6881\u533a', 'level': 3 },
          '2346': { 'id': 2346, 'pid': 2324, 'name': '\u6f7c\u5357\u533a', 'level': 3 },
          '2347': { 'id': 2347, 'pid': 2324, 'name': '\u8363\u660c\u533a', 'level': 3 },
          '2348': { 'id': 2348, 'pid': 2324, 'name': '\u6881\u5e73\u533a', 'level': 3 }
        }
      }, '2363': {
        'id': 2363, 'pid': 2323, 'name': '\u53bf', 'level': 2, 'region': {
          '2349': { 'id': 2349, 'pid': 2363, 'name': '\u57ce\u53e3\u53bf', 'level': 3 },
          '2350': { 'id': 2350, 'pid': 2363, 'name': '\u4e30\u90fd\u53bf', 'level': 3 },
          '2351': { 'id': 2351, 'pid': 2363, 'name': '\u57ab\u6c5f\u53bf', 'level': 3 },
          '2352': { 'id': 2352, 'pid': 2363, 'name': '\u6b66\u9686\u53bf', 'level': 3 },
          '2353': { 'id': 2353, 'pid': 2363, 'name': '\u5fe0\u53bf', 'level': 3 },
          '2354': { 'id': 2354, 'pid': 2363, 'name': '\u5f00\u53bf', 'level': 3 },
          '2355': { 'id': 2355, 'pid': 2363, 'name': '\u4e91\u9633\u53bf', 'level': 3 },
          '2356': { 'id': 2356, 'pid': 2363, 'name': '\u5949\u8282\u53bf', 'level': 3 },
          '2357': { 'id': 2357, 'pid': 2363, 'name': '\u5deb\u5c71\u53bf', 'level': 3 },
          '2358': { 'id': 2358, 'pid': 2363, 'name': '\u5deb\u6eaa\u53bf', 'level': 3 },
          '2359': { 'id': 2359, 'pid': 2363, 'name': '\u77f3\u67f1\u571f\u5bb6\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2360': {
            'id': 2360,
            'pid': 2363,
            'name': '\u79c0\u5c71\u571f\u5bb6\u65cf\u82d7\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2361': {
            'id': 2361,
            'pid': 2363,
            'name': '\u9149\u9633\u571f\u5bb6\u65cf\u82d7\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2362': {
            'id': 2362,
            'pid': 2363,
            'name': '\u5f6d\u6c34\u82d7\u65cf\u571f\u5bb6\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          }
        }
      }
    }
  },
  '2367': {
    'id': 2367, 'pid': 0, 'name': '\u56db\u5ddd\u7701', 'level': 1, 'city': {
      '2368': {
        'id': 2368, 'pid': 2367, 'name': '\u6210\u90fd\u5e02', 'level': 2, 'region': {
          '2369': { 'id': 2369, 'pid': 2368, 'name': '\u9526\u6c5f\u533a', 'level': 3 },
          '2370': { 'id': 2370, 'pid': 2368, 'name': '\u9752\u7f8a\u533a', 'level': 3 },
          '2371': { 'id': 2371, 'pid': 2368, 'name': '\u91d1\u725b\u533a', 'level': 3 },
          '2372': { 'id': 2372, 'pid': 2368, 'name': '\u6b66\u4faf\u533a', 'level': 3 },
          '2373': { 'id': 2373, 'pid': 2368, 'name': '\u6210\u534e\u533a', 'level': 3 },
          '2374': { 'id': 2374, 'pid': 2368, 'name': '\u9f99\u6cc9\u9a7f\u533a', 'level': 3 },
          '2375': { 'id': 2375, 'pid': 2368, 'name': '\u9752\u767d\u6c5f\u533a', 'level': 3 },
          '2376': { 'id': 2376, 'pid': 2368, 'name': '\u65b0\u90fd\u533a', 'level': 3 },
          '2377': { 'id': 2377, 'pid': 2368, 'name': '\u6e29\u6c5f\u533a', 'level': 3 },
          '2378': { 'id': 2378, 'pid': 2368, 'name': '\u91d1\u5802\u53bf', 'level': 3 },
          '2379': { 'id': 2379, 'pid': 2368, 'name': '\u53cc\u6d41\u53bf', 'level': 3 },
          '2380': { 'id': 2380, 'pid': 2368, 'name': '\u90eb\u53bf', 'level': 3 },
          '2381': { 'id': 2381, 'pid': 2368, 'name': '\u5927\u9091\u53bf', 'level': 3 },
          '2382': { 'id': 2382, 'pid': 2368, 'name': '\u84b2\u6c5f\u53bf', 'level': 3 },
          '2383': { 'id': 2383, 'pid': 2368, 'name': '\u65b0\u6d25\u53bf', 'level': 3 },
          '2384': { 'id': 2384, 'pid': 2368, 'name': '\u90fd\u6c5f\u5830\u5e02', 'level': 3 },
          '2385': { 'id': 2385, 'pid': 2368, 'name': '\u5f6d\u5dde\u5e02', 'level': 3 },
          '2386': { 'id': 2386, 'pid': 2368, 'name': '\u909b\u5d03\u5e02', 'level': 3 },
          '2387': { 'id': 2387, 'pid': 2368, 'name': '\u5d07\u5dde\u5e02', 'level': 3 }
        }
      },
      '2388': {
        'id': 2388,
        'pid': 2367,
        'name': '\u81ea\u8d21\u5e02',
        'level': 2,
        'region': {
          '2389': { 'id': 2389, 'pid': 2388, 'name': '\u81ea\u6d41\u4e95\u533a', 'level': 3 },
          '2390': { 'id': 2390, 'pid': 2388, 'name': '\u8d21\u4e95\u533a', 'level': 3 },
          '2391': { 'id': 2391, 'pid': 2388, 'name': '\u5927\u5b89\u533a', 'level': 3 },
          '2392': { 'id': 2392, 'pid': 2388, 'name': '\u6cbf\u6ee9\u533a', 'level': 3 },
          '2393': { 'id': 2393, 'pid': 2388, 'name': '\u8363\u53bf', 'level': 3 },
          '2394': { 'id': 2394, 'pid': 2388, 'name': '\u5bcc\u987a\u53bf', 'level': 3 }
        }
      },
      '2395': {
        'id': 2395,
        'pid': 2367,
        'name': '\u6500\u679d\u82b1\u5e02',
        'level': 2,
        'region': {
          '2396': { 'id': 2396, 'pid': 2395, 'name': '\u4e1c\u533a', 'level': 3 },
          '2397': { 'id': 2397, 'pid': 2395, 'name': '\u897f\u533a', 'level': 3 },
          '2398': { 'id': 2398, 'pid': 2395, 'name': '\u4ec1\u548c\u533a', 'level': 3 },
          '2399': { 'id': 2399, 'pid': 2395, 'name': '\u7c73\u6613\u53bf', 'level': 3 },
          '2400': { 'id': 2400, 'pid': 2395, 'name': '\u76d0\u8fb9\u53bf', 'level': 3 }
        }
      },
      '2401': {
        'id': 2401,
        'pid': 2367,
        'name': '\u6cf8\u5dde\u5e02',
        'level': 2,
        'region': {
          '2402': { 'id': 2402, 'pid': 2401, 'name': '\u6c5f\u9633\u533a', 'level': 3 },
          '2403': { 'id': 2403, 'pid': 2401, 'name': '\u7eb3\u6eaa\u533a', 'level': 3 },
          '2404': { 'id': 2404, 'pid': 2401, 'name': '\u9f99\u9a6c\u6f6d\u533a', 'level': 3 },
          '2405': { 'id': 2405, 'pid': 2401, 'name': '\u6cf8\u53bf', 'level': 3 },
          '2406': { 'id': 2406, 'pid': 2401, 'name': '\u5408\u6c5f\u53bf', 'level': 3 },
          '2407': { 'id': 2407, 'pid': 2401, 'name': '\u53d9\u6c38\u53bf', 'level': 3 },
          '2408': { 'id': 2408, 'pid': 2401, 'name': '\u53e4\u853a\u53bf', 'level': 3 }
        }
      },
      '2409': {
        'id': 2409,
        'pid': 2367,
        'name': '\u5fb7\u9633\u5e02',
        'level': 2,
        'region': {
          '2410': { 'id': 2410, 'pid': 2409, 'name': '\u65cc\u9633\u533a', 'level': 3 },
          '2411': { 'id': 2411, 'pid': 2409, 'name': '\u4e2d\u6c5f\u53bf', 'level': 3 },
          '2412': { 'id': 2412, 'pid': 2409, 'name': '\u7f57\u6c5f\u53bf', 'level': 3 },
          '2413': { 'id': 2413, 'pid': 2409, 'name': '\u5e7f\u6c49\u5e02', 'level': 3 },
          '2414': { 'id': 2414, 'pid': 2409, 'name': '\u4ec0\u90a1\u5e02', 'level': 3 },
          '2415': { 'id': 2415, 'pid': 2409, 'name': '\u7ef5\u7af9\u5e02', 'level': 3 }
        }
      },
      '2416': {
        'id': 2416,
        'pid': 2367,
        'name': '\u7ef5\u9633\u5e02',
        'level': 2,
        'region': {
          '2417': { 'id': 2417, 'pid': 2416, 'name': '\u6daa\u57ce\u533a', 'level': 3 },
          '2418': { 'id': 2418, 'pid': 2416, 'name': '\u6e38\u4ed9\u533a', 'level': 3 },
          '2419': { 'id': 2419, 'pid': 2416, 'name': '\u4e09\u53f0\u53bf', 'level': 3 },
          '2420': { 'id': 2420, 'pid': 2416, 'name': '\u76d0\u4ead\u53bf', 'level': 3 },
          '2421': { 'id': 2421, 'pid': 2416, 'name': '\u5b89\u53bf', 'level': 3 },
          '2422': { 'id': 2422, 'pid': 2416, 'name': '\u6893\u6f7c\u53bf', 'level': 3 },
          '2423': { 'id': 2423, 'pid': 2416, 'name': '\u5317\u5ddd\u7f8c\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2424': { 'id': 2424, 'pid': 2416, 'name': '\u5e73\u6b66\u53bf', 'level': 3 },
          '2425': { 'id': 2425, 'pid': 2416, 'name': '\u6c5f\u6cb9\u5e02', 'level': 3 }
        }
      },
      '2426': {
        'id': 2426,
        'pid': 2367,
        'name': '\u5e7f\u5143\u5e02',
        'level': 2,
        'region': {
          '2427': { 'id': 2427, 'pid': 2426, 'name': '\u5229\u5dde\u533a', 'level': 3 },
          '2428': { 'id': 2428, 'pid': 2426, 'name': '\u662d\u5316\u533a', 'level': 3 },
          '2429': { 'id': 2429, 'pid': 2426, 'name': '\u671d\u5929\u533a', 'level': 3 },
          '2430': { 'id': 2430, 'pid': 2426, 'name': '\u65fa\u82cd\u53bf', 'level': 3 },
          '2431': { 'id': 2431, 'pid': 2426, 'name': '\u9752\u5ddd\u53bf', 'level': 3 },
          '2432': { 'id': 2432, 'pid': 2426, 'name': '\u5251\u9601\u53bf', 'level': 3 },
          '2433': { 'id': 2433, 'pid': 2426, 'name': '\u82cd\u6eaa\u53bf', 'level': 3 }
        }
      },
      '2434': {
        'id': 2434,
        'pid': 2367,
        'name': '\u9042\u5b81\u5e02',
        'level': 2,
        'region': {
          '2435': { 'id': 2435, 'pid': 2434, 'name': '\u8239\u5c71\u533a', 'level': 3 },
          '2436': { 'id': 2436, 'pid': 2434, 'name': '\u5b89\u5c45\u533a', 'level': 3 },
          '2437': { 'id': 2437, 'pid': 2434, 'name': '\u84ec\u6eaa\u53bf', 'level': 3 },
          '2438': { 'id': 2438, 'pid': 2434, 'name': '\u5c04\u6d2a\u53bf', 'level': 3 },
          '2439': { 'id': 2439, 'pid': 2434, 'name': '\u5927\u82f1\u53bf', 'level': 3 }
        }
      },
      '2440': {
        'id': 2440,
        'pid': 2367,
        'name': '\u5185\u6c5f\u5e02',
        'level': 2,
        'region': {
          '2441': { 'id': 2441, 'pid': 2440, 'name': '\u5e02\u4e2d\u533a', 'level': 3 },
          '2442': { 'id': 2442, 'pid': 2440, 'name': '\u4e1c\u5174\u533a', 'level': 3 },
          '2443': { 'id': 2443, 'pid': 2440, 'name': '\u5a01\u8fdc\u53bf', 'level': 3 },
          '2444': { 'id': 2444, 'pid': 2440, 'name': '\u8d44\u4e2d\u53bf', 'level': 3 },
          '2445': { 'id': 2445, 'pid': 2440, 'name': '\u9686\u660c\u53bf', 'level': 3 }
        }
      },
      '2446': {
        'id': 2446,
        'pid': 2367,
        'name': '\u4e50\u5c71\u5e02',
        'level': 2,
        'region': {
          '2447': { 'id': 2447, 'pid': 2446, 'name': '\u5e02\u4e2d\u533a', 'level': 3 },
          '2448': { 'id': 2448, 'pid': 2446, 'name': '\u6c99\u6e7e\u533a', 'level': 3 },
          '2449': { 'id': 2449, 'pid': 2446, 'name': '\u4e94\u901a\u6865\u533a', 'level': 3 },
          '2450': { 'id': 2450, 'pid': 2446, 'name': '\u91d1\u53e3\u6cb3\u533a', 'level': 3 },
          '2451': { 'id': 2451, 'pid': 2446, 'name': '\u728d\u4e3a\u53bf', 'level': 3 },
          '2452': { 'id': 2452, 'pid': 2446, 'name': '\u4e95\u7814\u53bf', 'level': 3 },
          '2453': { 'id': 2453, 'pid': 2446, 'name': '\u5939\u6c5f\u53bf', 'level': 3 },
          '2454': { 'id': 2454, 'pid': 2446, 'name': '\u6c90\u5ddd\u53bf', 'level': 3 },
          '2455': { 'id': 2455, 'pid': 2446, 'name': '\u5ce8\u8fb9\u5f5d\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2456': { 'id': 2456, 'pid': 2446, 'name': '\u9a6c\u8fb9\u5f5d\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2457': { 'id': 2457, 'pid': 2446, 'name': '\u5ce8\u7709\u5c71\u5e02', 'level': 3 }
        }
      },
      '2458': {
        'id': 2458,
        'pid': 2367,
        'name': '\u5357\u5145\u5e02',
        'level': 2,
        'region': {
          '2459': { 'id': 2459, 'pid': 2458, 'name': '\u987a\u5e86\u533a', 'level': 3 },
          '2460': { 'id': 2460, 'pid': 2458, 'name': '\u9ad8\u576a\u533a', 'level': 3 },
          '2461': { 'id': 2461, 'pid': 2458, 'name': '\u5609\u9675\u533a', 'level': 3 },
          '2462': { 'id': 2462, 'pid': 2458, 'name': '\u5357\u90e8\u53bf', 'level': 3 },
          '2463': { 'id': 2463, 'pid': 2458, 'name': '\u8425\u5c71\u53bf', 'level': 3 },
          '2464': { 'id': 2464, 'pid': 2458, 'name': '\u84ec\u5b89\u53bf', 'level': 3 },
          '2465': { 'id': 2465, 'pid': 2458, 'name': '\u4eea\u9647\u53bf', 'level': 3 },
          '2466': { 'id': 2466, 'pid': 2458, 'name': '\u897f\u5145\u53bf', 'level': 3 },
          '2467': { 'id': 2467, 'pid': 2458, 'name': '\u9606\u4e2d\u5e02', 'level': 3 }
        }
      },
      '2468': {
        'id': 2468,
        'pid': 2367,
        'name': '\u7709\u5c71\u5e02',
        'level': 2,
        'region': {
          '2469': { 'id': 2469, 'pid': 2468, 'name': '\u4e1c\u5761\u533a', 'level': 3 },
          '2470': { 'id': 2470, 'pid': 2468, 'name': '\u5f6d\u5c71\u533a', 'level': 3 },
          '2471': { 'id': 2471, 'pid': 2468, 'name': '\u4ec1\u5bff\u53bf', 'level': 3 },
          '2472': { 'id': 2472, 'pid': 2468, 'name': '\u6d2a\u96c5\u53bf', 'level': 3 },
          '2473': { 'id': 2473, 'pid': 2468, 'name': '\u4e39\u68f1\u53bf', 'level': 3 },
          '2474': { 'id': 2474, 'pid': 2468, 'name': '\u9752\u795e\u53bf', 'level': 3 }
        }
      },
      '2475': {
        'id': 2475,
        'pid': 2367,
        'name': '\u5b9c\u5bbe\u5e02',
        'level': 2,
        'region': {
          '2476': { 'id': 2476, 'pid': 2475, 'name': '\u7fe0\u5c4f\u533a', 'level': 3 },
          '2477': { 'id': 2477, 'pid': 2475, 'name': '\u5357\u6eaa\u533a', 'level': 3 },
          '2478': { 'id': 2478, 'pid': 2475, 'name': '\u5b9c\u5bbe\u53bf', 'level': 3 },
          '2479': { 'id': 2479, 'pid': 2475, 'name': '\u6c5f\u5b89\u53bf', 'level': 3 },
          '2480': { 'id': 2480, 'pid': 2475, 'name': '\u957f\u5b81\u53bf', 'level': 3 },
          '2481': { 'id': 2481, 'pid': 2475, 'name': '\u9ad8\u53bf', 'level': 3 },
          '2482': { 'id': 2482, 'pid': 2475, 'name': '\u73d9\u53bf', 'level': 3 },
          '2483': { 'id': 2483, 'pid': 2475, 'name': '\u7b60\u8fde\u53bf', 'level': 3 },
          '2484': { 'id': 2484, 'pid': 2475, 'name': '\u5174\u6587\u53bf', 'level': 3 },
          '2485': { 'id': 2485, 'pid': 2475, 'name': '\u5c4f\u5c71\u53bf', 'level': 3 }
        }
      },
      '2486': {
        'id': 2486,
        'pid': 2367,
        'name': '\u5e7f\u5b89\u5e02',
        'level': 2,
        'region': {
          '2487': { 'id': 2487, 'pid': 2486, 'name': '\u5e7f\u5b89\u533a', 'level': 3 },
          '2488': { 'id': 2488, 'pid': 2486, 'name': '\u524d\u950b\u533a', 'level': 3 },
          '2489': { 'id': 2489, 'pid': 2486, 'name': '\u5cb3\u6c60\u53bf', 'level': 3 },
          '2490': { 'id': 2490, 'pid': 2486, 'name': '\u6b66\u80dc\u53bf', 'level': 3 },
          '2491': { 'id': 2491, 'pid': 2486, 'name': '\u90bb\u6c34\u53bf', 'level': 3 },
          '2492': { 'id': 2492, 'pid': 2486, 'name': '\u534e\u84e5\u5e02', 'level': 3 }
        }
      },
      '2493': {
        'id': 2493,
        'pid': 2367,
        'name': '\u8fbe\u5dde\u5e02',
        'level': 2,
        'region': {
          '2494': { 'id': 2494, 'pid': 2493, 'name': '\u901a\u5ddd\u533a', 'level': 3 },
          '2495': { 'id': 2495, 'pid': 2493, 'name': '\u8fbe\u5ddd\u533a', 'level': 3 },
          '2496': { 'id': 2496, 'pid': 2493, 'name': '\u5ba3\u6c49\u53bf', 'level': 3 },
          '2497': { 'id': 2497, 'pid': 2493, 'name': '\u5f00\u6c5f\u53bf', 'level': 3 },
          '2498': { 'id': 2498, 'pid': 2493, 'name': '\u5927\u7af9\u53bf', 'level': 3 },
          '2499': { 'id': 2499, 'pid': 2493, 'name': '\u6e20\u53bf', 'level': 3 },
          '2500': { 'id': 2500, 'pid': 2493, 'name': '\u4e07\u6e90\u5e02', 'level': 3 }
        }
      },
      '2501': {
        'id': 2501,
        'pid': 2367,
        'name': '\u96c5\u5b89\u5e02',
        'level': 2,
        'region': {
          '2502': { 'id': 2502, 'pid': 2501, 'name': '\u96e8\u57ce\u533a', 'level': 3 },
          '2503': { 'id': 2503, 'pid': 2501, 'name': '\u540d\u5c71\u533a', 'level': 3 },
          '2504': { 'id': 2504, 'pid': 2501, 'name': '\u8365\u7ecf\u53bf', 'level': 3 },
          '2505': { 'id': 2505, 'pid': 2501, 'name': '\u6c49\u6e90\u53bf', 'level': 3 },
          '2506': { 'id': 2506, 'pid': 2501, 'name': '\u77f3\u68c9\u53bf', 'level': 3 },
          '2507': { 'id': 2507, 'pid': 2501, 'name': '\u5929\u5168\u53bf', 'level': 3 },
          '2508': { 'id': 2508, 'pid': 2501, 'name': '\u82a6\u5c71\u53bf', 'level': 3 },
          '2509': { 'id': 2509, 'pid': 2501, 'name': '\u5b9d\u5174\u53bf', 'level': 3 }
        }
      },
      '2510': {
        'id': 2510,
        'pid': 2367,
        'name': '\u5df4\u4e2d\u5e02',
        'level': 2,
        'region': {
          '2511': { 'id': 2511, 'pid': 2510, 'name': '\u5df4\u5dde\u533a', 'level': 3 },
          '2512': { 'id': 2512, 'pid': 2510, 'name': '\u6069\u9633\u533a', 'level': 3 },
          '2513': { 'id': 2513, 'pid': 2510, 'name': '\u901a\u6c5f\u53bf', 'level': 3 },
          '2514': { 'id': 2514, 'pid': 2510, 'name': '\u5357\u6c5f\u53bf', 'level': 3 },
          '2515': { 'id': 2515, 'pid': 2510, 'name': '\u5e73\u660c\u53bf', 'level': 3 }
        }
      },
      '2516': {
        'id': 2516,
        'pid': 2367,
        'name': '\u8d44\u9633\u5e02',
        'level': 2,
        'region': {
          '2517': { 'id': 2517, 'pid': 2516, 'name': '\u96c1\u6c5f\u533a', 'level': 3 },
          '2518': { 'id': 2518, 'pid': 2516, 'name': '\u5b89\u5cb3\u53bf', 'level': 3 },
          '2519': { 'id': 2519, 'pid': 2516, 'name': '\u4e50\u81f3\u53bf', 'level': 3 },
          '2520': { 'id': 2520, 'pid': 2516, 'name': '\u7b80\u9633\u5e02', 'level': 3 }
        }
      },
      '2521': {
        'id': 2521,
        'pid': 2367,
        'name': '\u963f\u575d\u85cf\u65cf\u7f8c\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '2522': { 'id': 2522, 'pid': 2521, 'name': '\u6c76\u5ddd\u53bf', 'level': 3 },
          '2523': { 'id': 2523, 'pid': 2521, 'name': '\u7406\u53bf', 'level': 3 },
          '2524': { 'id': 2524, 'pid': 2521, 'name': '\u8302\u53bf', 'level': 3 },
          '2525': { 'id': 2525, 'pid': 2521, 'name': '\u677e\u6f58\u53bf', 'level': 3 },
          '2526': { 'id': 2526, 'pid': 2521, 'name': '\u4e5d\u5be8\u6c9f\u53bf', 'level': 3 },
          '2527': { 'id': 2527, 'pid': 2521, 'name': '\u91d1\u5ddd\u53bf', 'level': 3 },
          '2528': { 'id': 2528, 'pid': 2521, 'name': '\u5c0f\u91d1\u53bf', 'level': 3 },
          '2529': { 'id': 2529, 'pid': 2521, 'name': '\u9ed1\u6c34\u53bf', 'level': 3 },
          '2530': { 'id': 2530, 'pid': 2521, 'name': '\u9a6c\u5c14\u5eb7\u53bf', 'level': 3 },
          '2531': { 'id': 2531, 'pid': 2521, 'name': '\u58e4\u5858\u53bf', 'level': 3 },
          '2532': { 'id': 2532, 'pid': 2521, 'name': '\u963f\u575d\u53bf', 'level': 3 },
          '2533': { 'id': 2533, 'pid': 2521, 'name': '\u82e5\u5c14\u76d6\u53bf', 'level': 3 },
          '2534': { 'id': 2534, 'pid': 2521, 'name': '\u7ea2\u539f\u53bf', 'level': 3 }
        }
      },
      '2535': {
        'id': 2535, 'pid': 2367, 'name': '\u7518\u5b5c\u85cf\u65cf\u81ea\u6cbb\u5dde', 'level': 2, 'region': {
          '2536': { 'id': 2536, 'pid': 2535, 'name': '\u5eb7\u5b9a\u53bf', 'level': 3 },
          '2537': { 'id': 2537, 'pid': 2535, 'name': '\u6cf8\u5b9a\u53bf', 'level': 3 },
          '2538': { 'id': 2538, 'pid': 2535, 'name': '\u4e39\u5df4\u53bf', 'level': 3 },
          '2539': { 'id': 2539, 'pid': 2535, 'name': '\u4e5d\u9f99\u53bf', 'level': 3 },
          '2540': { 'id': 2540, 'pid': 2535, 'name': '\u96c5\u6c5f\u53bf', 'level': 3 },
          '2541': { 'id': 2541, 'pid': 2535, 'name': '\u9053\u5b5a\u53bf', 'level': 3 },
          '2542': { 'id': 2542, 'pid': 2535, 'name': '\u7089\u970d\u53bf', 'level': 3 },
          '2543': { 'id': 2543, 'pid': 2535, 'name': '\u7518\u5b5c\u53bf', 'level': 3 },
          '2544': { 'id': 2544, 'pid': 2535, 'name': '\u65b0\u9f99\u53bf', 'level': 3 },
          '2545': { 'id': 2545, 'pid': 2535, 'name': '\u5fb7\u683c\u53bf', 'level': 3 },
          '2546': { 'id': 2546, 'pid': 2535, 'name': '\u767d\u7389\u53bf', 'level': 3 },
          '2547': { 'id': 2547, 'pid': 2535, 'name': '\u77f3\u6e20\u53bf', 'level': 3 },
          '2548': { 'id': 2548, 'pid': 2535, 'name': '\u8272\u8fbe\u53bf', 'level': 3 },
          '2549': { 'id': 2549, 'pid': 2535, 'name': '\u7406\u5858\u53bf', 'level': 3 },
          '2550': { 'id': 2550, 'pid': 2535, 'name': '\u5df4\u5858\u53bf', 'level': 3 },
          '2551': { 'id': 2551, 'pid': 2535, 'name': '\u4e61\u57ce\u53bf', 'level': 3 },
          '2552': { 'id': 2552, 'pid': 2535, 'name': '\u7a3b\u57ce\u53bf', 'level': 3 },
          '2553': { 'id': 2553, 'pid': 2535, 'name': '\u5f97\u8363\u53bf', 'level': 3 }
        }
      },
      '2554': {
        'id': 2554, 'pid': 2367, 'name': '\u51c9\u5c71\u5f5d\u65cf\u81ea\u6cbb\u5dde', 'level': 2, 'region': {
          '2555': { 'id': 2555, 'pid': 2554, 'name': '\u897f\u660c\u5e02', 'level': 3 },
          '2556': { 'id': 2556, 'pid': 2554, 'name': '\u6728\u91cc\u85cf\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2557': { 'id': 2557, 'pid': 2554, 'name': '\u76d0\u6e90\u53bf', 'level': 3 },
          '2558': { 'id': 2558, 'pid': 2554, 'name': '\u5fb7\u660c\u53bf', 'level': 3 },
          '2559': { 'id': 2559, 'pid': 2554, 'name': '\u4f1a\u7406\u53bf', 'level': 3 },
          '2560': { 'id': 2560, 'pid': 2554, 'name': '\u4f1a\u4e1c\u53bf', 'level': 3 },
          '2561': { 'id': 2561, 'pid': 2554, 'name': '\u5b81\u5357\u53bf', 'level': 3 },
          '2562': { 'id': 2562, 'pid': 2554, 'name': '\u666e\u683c\u53bf', 'level': 3 },
          '2563': { 'id': 2563, 'pid': 2554, 'name': '\u5e03\u62d6\u53bf', 'level': 3 },
          '2564': { 'id': 2564, 'pid': 2554, 'name': '\u91d1\u9633\u53bf', 'level': 3 },
          '2565': { 'id': 2565, 'pid': 2554, 'name': '\u662d\u89c9\u53bf', 'level': 3 },
          '2566': { 'id': 2566, 'pid': 2554, 'name': '\u559c\u5fb7\u53bf', 'level': 3 },
          '2567': { 'id': 2567, 'pid': 2554, 'name': '\u5195\u5b81\u53bf', 'level': 3 },
          '2568': { 'id': 2568, 'pid': 2554, 'name': '\u8d8a\u897f\u53bf', 'level': 3 },
          '2569': { 'id': 2569, 'pid': 2554, 'name': '\u7518\u6d1b\u53bf', 'level': 3 },
          '2570': { 'id': 2570, 'pid': 2554, 'name': '\u7f8e\u59d1\u53bf', 'level': 3 },
          '2571': { 'id': 2571, 'pid': 2554, 'name': '\u96f7\u6ce2\u53bf', 'level': 3 }
        }
      }
    }
  },
  '2572': {
    'id': 2572, 'pid': 0, 'name': '\u8d35\u5dde\u7701', 'level': 1, 'city': {
      '2573': {
        'id': 2573,
        'pid': 2572,
        'name': '\u8d35\u9633\u5e02',
        'level': 2,
        'region': {
          '2574': { 'id': 2574, 'pid': 2573, 'name': '\u5357\u660e\u533a', 'level': 3 },
          '2575': { 'id': 2575, 'pid': 2573, 'name': '\u4e91\u5ca9\u533a', 'level': 3 },
          '2576': { 'id': 2576, 'pid': 2573, 'name': '\u82b1\u6eaa\u533a', 'level': 3 },
          '2577': { 'id': 2577, 'pid': 2573, 'name': '\u4e4c\u5f53\u533a', 'level': 3 },
          '2578': { 'id': 2578, 'pid': 2573, 'name': '\u767d\u4e91\u533a', 'level': 3 },
          '2579': { 'id': 2579, 'pid': 2573, 'name': '\u89c2\u5c71\u6e56\u533a', 'level': 3 },
          '2580': { 'id': 2580, 'pid': 2573, 'name': '\u5f00\u9633\u53bf', 'level': 3 },
          '2581': { 'id': 2581, 'pid': 2573, 'name': '\u606f\u70fd\u53bf', 'level': 3 },
          '2582': { 'id': 2582, 'pid': 2573, 'name': '\u4fee\u6587\u53bf', 'level': 3 },
          '2583': { 'id': 2583, 'pid': 2573, 'name': '\u6e05\u9547\u5e02', 'level': 3 }
        }
      },
      '2584': {
        'id': 2584,
        'pid': 2572,
        'name': '\u516d\u76d8\u6c34\u5e02',
        'level': 2,
        'region': {
          '2585': { 'id': 2585, 'pid': 2584, 'name': '\u949f\u5c71\u533a', 'level': 3 },
          '2586': { 'id': 2586, 'pid': 2584, 'name': '\u516d\u679d\u7279\u533a', 'level': 3 },
          '2587': { 'id': 2587, 'pid': 2584, 'name': '\u6c34\u57ce\u53bf', 'level': 3 },
          '2588': { 'id': 2588, 'pid': 2584, 'name': '\u76d8\u53bf', 'level': 3 }
        }
      },
      '2589': {
        'id': 2589, 'pid': 2572, 'name': '\u9075\u4e49\u5e02', 'level': 2, 'region': {
          '2590': { 'id': 2590, 'pid': 2589, 'name': '\u7ea2\u82b1\u5c97\u533a', 'level': 3 },
          '2591': { 'id': 2591, 'pid': 2589, 'name': '\u6c47\u5ddd\u533a', 'level': 3 },
          '2592': { 'id': 2592, 'pid': 2589, 'name': '\u9075\u4e49\u53bf', 'level': 3 },
          '2593': { 'id': 2593, 'pid': 2589, 'name': '\u6850\u6893\u53bf', 'level': 3 },
          '2594': { 'id': 2594, 'pid': 2589, 'name': '\u7ee5\u9633\u53bf', 'level': 3 },
          '2595': { 'id': 2595, 'pid': 2589, 'name': '\u6b63\u5b89\u53bf', 'level': 3 },
          '2596': {
            'id': 2596,
            'pid': 2589,
            'name': '\u9053\u771f\u4ee1\u4f6c\u65cf\u82d7\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2597': {
            'id': 2597,
            'pid': 2589,
            'name': '\u52a1\u5ddd\u4ee1\u4f6c\u65cf\u82d7\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2598': { 'id': 2598, 'pid': 2589, 'name': '\u51e4\u5188\u53bf', 'level': 3 },
          '2599': { 'id': 2599, 'pid': 2589, 'name': '\u6e44\u6f6d\u53bf', 'level': 3 },
          '2600': { 'id': 2600, 'pid': 2589, 'name': '\u4f59\u5e86\u53bf', 'level': 3 },
          '2601': { 'id': 2601, 'pid': 2589, 'name': '\u4e60\u6c34\u53bf', 'level': 3 },
          '2602': { 'id': 2602, 'pid': 2589, 'name': '\u8d64\u6c34\u5e02', 'level': 3 },
          '2603': { 'id': 2603, 'pid': 2589, 'name': '\u4ec1\u6000\u5e02', 'level': 3 }
        }
      },
      '2604': {
        'id': 2604,
        'pid': 2572,
        'name': '\u5b89\u987a\u5e02',
        'level': 2,
        'region': {
          '2605': { 'id': 2605, 'pid': 2604, 'name': '\u897f\u79c0\u533a', 'level': 3 },
          '2606': { 'id': 2606, 'pid': 2604, 'name': '\u5e73\u575d\u533a', 'level': 3 },
          '2607': { 'id': 2607, 'pid': 2604, 'name': '\u666e\u5b9a\u53bf', 'level': 3 },
          '2608': {
            'id': 2608,
            'pid': 2604,
            'name': '\u9547\u5b81\u5e03\u4f9d\u65cf\u82d7\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2609': {
            'id': 2609,
            'pid': 2604,
            'name': '\u5173\u5cad\u5e03\u4f9d\u65cf\u82d7\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2610': {
            'id': 2610,
            'pid': 2604,
            'name': '\u7d2b\u4e91\u82d7\u65cf\u5e03\u4f9d\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          }
        }
      },
      '2611': {
        'id': 2611,
        'pid': 2572,
        'name': '\u6bd5\u8282\u5e02',
        'level': 2,
        'region': {
          '2612': { 'id': 2612, 'pid': 2611, 'name': '\u4e03\u661f\u5173\u533a', 'level': 3 },
          '2613': { 'id': 2613, 'pid': 2611, 'name': '\u5927\u65b9\u53bf', 'level': 3 },
          '2614': { 'id': 2614, 'pid': 2611, 'name': '\u9ed4\u897f\u53bf', 'level': 3 },
          '2615': { 'id': 2615, 'pid': 2611, 'name': '\u91d1\u6c99\u53bf', 'level': 3 },
          '2616': { 'id': 2616, 'pid': 2611, 'name': '\u7ec7\u91d1\u53bf', 'level': 3 },
          '2617': { 'id': 2617, 'pid': 2611, 'name': '\u7eb3\u96cd\u53bf', 'level': 3 },
          '2618': {
            'id': 2618,
            'pid': 2611,
            'name': '\u5a01\u5b81\u5f5d\u65cf\u56de\u65cf\u82d7\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2619': { 'id': 2619, 'pid': 2611, 'name': '\u8d6b\u7ae0\u53bf', 'level': 3 }
        }
      },
      '2620': {
        'id': 2620,
        'pid': 2572,
        'name': '\u94dc\u4ec1\u5e02',
        'level': 2,
        'region': {
          '2621': { 'id': 2621, 'pid': 2620, 'name': '\u78a7\u6c5f\u533a', 'level': 3 },
          '2622': { 'id': 2622, 'pid': 2620, 'name': '\u4e07\u5c71\u533a', 'level': 3 },
          '2623': { 'id': 2623, 'pid': 2620, 'name': '\u6c5f\u53e3\u53bf', 'level': 3 },
          '2624': { 'id': 2624, 'pid': 2620, 'name': '\u7389\u5c4f\u4f97\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2625': { 'id': 2625, 'pid': 2620, 'name': '\u77f3\u9621\u53bf', 'level': 3 },
          '2626': { 'id': 2626, 'pid': 2620, 'name': '\u601d\u5357\u53bf', 'level': 3 },
          '2627': {
            'id': 2627,
            'pid': 2620,
            'name': '\u5370\u6c5f\u571f\u5bb6\u65cf\u82d7\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2628': { 'id': 2628, 'pid': 2620, 'name': '\u5fb7\u6c5f\u53bf', 'level': 3 },
          '2629': { 'id': 2629, 'pid': 2620, 'name': '\u6cbf\u6cb3\u571f\u5bb6\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2630': { 'id': 2630, 'pid': 2620, 'name': '\u677e\u6843\u82d7\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '2631': {
        'id': 2631,
        'pid': 2572,
        'name': '\u9ed4\u897f\u5357\u5e03\u4f9d\u65cf\u82d7\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '2632': { 'id': 2632, 'pid': 2631, 'name': '\u5174\u4e49\u5e02 ', 'level': 3 },
          '2633': { 'id': 2633, 'pid': 2631, 'name': '\u5174\u4ec1\u53bf', 'level': 3 },
          '2634': { 'id': 2634, 'pid': 2631, 'name': '\u666e\u5b89\u53bf', 'level': 3 },
          '2635': { 'id': 2635, 'pid': 2631, 'name': '\u6674\u9686\u53bf', 'level': 3 },
          '2636': { 'id': 2636, 'pid': 2631, 'name': '\u8d1e\u4e30\u53bf', 'level': 3 },
          '2637': { 'id': 2637, 'pid': 2631, 'name': '\u671b\u8c1f\u53bf', 'level': 3 },
          '2638': { 'id': 2638, 'pid': 2631, 'name': '\u518c\u4ea8\u53bf', 'level': 3 },
          '2639': { 'id': 2639, 'pid': 2631, 'name': '\u5b89\u9f99\u53bf', 'level': 3 }
        }
      },
      '2640': {
        'id': 2640,
        'pid': 2572,
        'name': '\u9ed4\u4e1c\u5357\u82d7\u65cf\u4f97\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '2641': { 'id': 2641, 'pid': 2640, 'name': '\u51ef\u91cc\u5e02', 'level': 3 },
          '2642': { 'id': 2642, 'pid': 2640, 'name': '\u9ec4\u5e73\u53bf', 'level': 3 },
          '2643': { 'id': 2643, 'pid': 2640, 'name': '\u65bd\u79c9\u53bf', 'level': 3 },
          '2644': { 'id': 2644, 'pid': 2640, 'name': '\u4e09\u7a57\u53bf', 'level': 3 },
          '2645': { 'id': 2645, 'pid': 2640, 'name': '\u9547\u8fdc\u53bf', 'level': 3 },
          '2646': { 'id': 2646, 'pid': 2640, 'name': '\u5c91\u5de9\u53bf', 'level': 3 },
          '2647': { 'id': 2647, 'pid': 2640, 'name': '\u5929\u67f1\u53bf', 'level': 3 },
          '2648': { 'id': 2648, 'pid': 2640, 'name': '\u9526\u5c4f\u53bf', 'level': 3 },
          '2649': { 'id': 2649, 'pid': 2640, 'name': '\u5251\u6cb3\u53bf', 'level': 3 },
          '2650': { 'id': 2650, 'pid': 2640, 'name': '\u53f0\u6c5f\u53bf', 'level': 3 },
          '2651': { 'id': 2651, 'pid': 2640, 'name': '\u9ece\u5e73\u53bf', 'level': 3 },
          '2652': { 'id': 2652, 'pid': 2640, 'name': '\u6995\u6c5f\u53bf', 'level': 3 },
          '2653': { 'id': 2653, 'pid': 2640, 'name': '\u4ece\u6c5f\u53bf', 'level': 3 },
          '2654': { 'id': 2654, 'pid': 2640, 'name': '\u96f7\u5c71\u53bf', 'level': 3 },
          '2655': { 'id': 2655, 'pid': 2640, 'name': '\u9ebb\u6c5f\u53bf', 'level': 3 },
          '2656': { 'id': 2656, 'pid': 2640, 'name': '\u4e39\u5be8\u53bf', 'level': 3 }
        }
      },
      '2657': {
        'id': 2657,
        'pid': 2572,
        'name': '\u9ed4\u5357\u5e03\u4f9d\u65cf\u82d7\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '2658': { 'id': 2658, 'pid': 2657, 'name': '\u90fd\u5300\u5e02', 'level': 3 },
          '2659': { 'id': 2659, 'pid': 2657, 'name': '\u798f\u6cc9\u5e02', 'level': 3 },
          '2660': { 'id': 2660, 'pid': 2657, 'name': '\u8354\u6ce2\u53bf', 'level': 3 },
          '2661': { 'id': 2661, 'pid': 2657, 'name': '\u8d35\u5b9a\u53bf', 'level': 3 },
          '2662': { 'id': 2662, 'pid': 2657, 'name': '\u74ee\u5b89\u53bf', 'level': 3 },
          '2663': { 'id': 2663, 'pid': 2657, 'name': '\u72ec\u5c71\u53bf', 'level': 3 },
          '2664': { 'id': 2664, 'pid': 2657, 'name': '\u5e73\u5858\u53bf', 'level': 3 },
          '2665': { 'id': 2665, 'pid': 2657, 'name': '\u7f57\u7538\u53bf', 'level': 3 },
          '2666': { 'id': 2666, 'pid': 2657, 'name': '\u957f\u987a\u53bf', 'level': 3 },
          '2667': { 'id': 2667, 'pid': 2657, 'name': '\u9f99\u91cc\u53bf', 'level': 3 },
          '2668': { 'id': 2668, 'pid': 2657, 'name': '\u60e0\u6c34\u53bf', 'level': 3 },
          '2669': { 'id': 2669, 'pid': 2657, 'name': '\u4e09\u90fd\u6c34\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      }
    }
  },
  '2670': {
    'id': 2670, 'pid': 0, 'name': '\u4e91\u5357\u7701', 'level': 1, 'city': {
      '2671': {
        'id': 2671, 'pid': 2670, 'name': '\u6606\u660e\u5e02', 'level': 2, 'region': {
          '2672': { 'id': 2672, 'pid': 2671, 'name': '\u4e94\u534e\u533a', 'level': 3 },
          '2673': { 'id': 2673, 'pid': 2671, 'name': '\u76d8\u9f99\u533a', 'level': 3 },
          '2674': { 'id': 2674, 'pid': 2671, 'name': '\u5b98\u6e21\u533a', 'level': 3 },
          '2675': { 'id': 2675, 'pid': 2671, 'name': '\u897f\u5c71\u533a', 'level': 3 },
          '2676': { 'id': 2676, 'pid': 2671, 'name': '\u4e1c\u5ddd\u533a', 'level': 3 },
          '2677': { 'id': 2677, 'pid': 2671, 'name': '\u5448\u8d21\u533a', 'level': 3 },
          '2678': { 'id': 2678, 'pid': 2671, 'name': '\u664b\u5b81\u53bf', 'level': 3 },
          '2679': { 'id': 2679, 'pid': 2671, 'name': '\u5bcc\u6c11\u53bf', 'level': 3 },
          '2680': { 'id': 2680, 'pid': 2671, 'name': '\u5b9c\u826f\u53bf', 'level': 3 },
          '2681': { 'id': 2681, 'pid': 2671, 'name': '\u77f3\u6797\u5f5d\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2682': { 'id': 2682, 'pid': 2671, 'name': '\u5d69\u660e\u53bf', 'level': 3 },
          '2683': {
            'id': 2683,
            'pid': 2671,
            'name': '\u7984\u529d\u5f5d\u65cf\u82d7\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2684': {
            'id': 2684,
            'pid': 2671,
            'name': '\u5bfb\u7538\u56de\u65cf\u5f5d\u65cf\u81ea\u6cbb\u53bf ',
            'level': 3
          },
          '2685': { 'id': 2685, 'pid': 2671, 'name': '\u5b89\u5b81\u5e02', 'level': 3 }
        }
      },
      '2686': {
        'id': 2686,
        'pid': 2670,
        'name': '\u66f2\u9756\u5e02',
        'level': 2,
        'region': {
          '2687': { 'id': 2687, 'pid': 2686, 'name': '\u9e92\u9e9f\u533a', 'level': 3 },
          '2688': { 'id': 2688, 'pid': 2686, 'name': '\u9a6c\u9f99\u53bf', 'level': 3 },
          '2689': { 'id': 2689, 'pid': 2686, 'name': '\u9646\u826f\u53bf', 'level': 3 },
          '2690': { 'id': 2690, 'pid': 2686, 'name': '\u5e08\u5b97\u53bf', 'level': 3 },
          '2691': { 'id': 2691, 'pid': 2686, 'name': '\u7f57\u5e73\u53bf', 'level': 3 },
          '2692': { 'id': 2692, 'pid': 2686, 'name': '\u5bcc\u6e90\u53bf', 'level': 3 },
          '2693': { 'id': 2693, 'pid': 2686, 'name': '\u4f1a\u6cfd\u53bf', 'level': 3 },
          '2694': { 'id': 2694, 'pid': 2686, 'name': '\u6cbe\u76ca\u53bf', 'level': 3 },
          '2695': { 'id': 2695, 'pid': 2686, 'name': '\u5ba3\u5a01\u5e02', 'level': 3 }
        }
      },
      '2696': {
        'id': 2696,
        'pid': 2670,
        'name': '\u7389\u6eaa\u5e02',
        'level': 2,
        'region': {
          '2697': { 'id': 2697, 'pid': 2696, 'name': '\u7ea2\u5854\u533a', 'level': 3 },
          '2698': { 'id': 2698, 'pid': 2696, 'name': '\u6c5f\u5ddd\u53bf', 'level': 3 },
          '2699': { 'id': 2699, 'pid': 2696, 'name': '\u6f84\u6c5f\u53bf', 'level': 3 },
          '2700': { 'id': 2700, 'pid': 2696, 'name': '\u901a\u6d77\u53bf', 'level': 3 },
          '2701': { 'id': 2701, 'pid': 2696, 'name': '\u534e\u5b81\u53bf', 'level': 3 },
          '2702': { 'id': 2702, 'pid': 2696, 'name': '\u6613\u95e8\u53bf', 'level': 3 },
          '2703': { 'id': 2703, 'pid': 2696, 'name': '\u5ce8\u5c71\u5f5d\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2704': {
            'id': 2704,
            'pid': 2696,
            'name': '\u65b0\u5e73\u5f5d\u65cf\u50a3\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2705': {
            'id': 2705,
            'pid': 2696,
            'name': '\u5143\u6c5f\u54c8\u5c3c\u65cf\u5f5d\u65cf\u50a3\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          }
        }
      },
      '2706': {
        'id': 2706,
        'pid': 2670,
        'name': '\u4fdd\u5c71\u5e02',
        'level': 2,
        'region': {
          '2707': { 'id': 2707, 'pid': 2706, 'name': '\u9686\u9633\u533a', 'level': 3 },
          '2708': { 'id': 2708, 'pid': 2706, 'name': '\u65bd\u7538\u53bf', 'level': 3 },
          '2709': { 'id': 2709, 'pid': 2706, 'name': '\u817e\u51b2\u53bf', 'level': 3 },
          '2710': { 'id': 2710, 'pid': 2706, 'name': '\u9f99\u9675\u53bf', 'level': 3 },
          '2711': { 'id': 2711, 'pid': 2706, 'name': '\u660c\u5b81\u53bf', 'level': 3 }
        }
      },
      '2712': {
        'id': 2712,
        'pid': 2670,
        'name': '\u662d\u901a\u5e02',
        'level': 2,
        'region': {
          '2713': { 'id': 2713, 'pid': 2712, 'name': '\u662d\u9633\u533a', 'level': 3 },
          '2714': { 'id': 2714, 'pid': 2712, 'name': '\u9c81\u7538\u53bf', 'level': 3 },
          '2715': { 'id': 2715, 'pid': 2712, 'name': '\u5de7\u5bb6\u53bf', 'level': 3 },
          '2716': { 'id': 2716, 'pid': 2712, 'name': '\u76d0\u6d25\u53bf', 'level': 3 },
          '2717': { 'id': 2717, 'pid': 2712, 'name': '\u5927\u5173\u53bf', 'level': 3 },
          '2718': { 'id': 2718, 'pid': 2712, 'name': '\u6c38\u5584\u53bf', 'level': 3 },
          '2719': { 'id': 2719, 'pid': 2712, 'name': '\u7ee5\u6c5f\u53bf', 'level': 3 },
          '2720': { 'id': 2720, 'pid': 2712, 'name': '\u9547\u96c4\u53bf', 'level': 3 },
          '2721': { 'id': 2721, 'pid': 2712, 'name': '\u5f5d\u826f\u53bf', 'level': 3 },
          '2722': { 'id': 2722, 'pid': 2712, 'name': '\u5a01\u4fe1\u53bf', 'level': 3 },
          '2723': { 'id': 2723, 'pid': 2712, 'name': '\u6c34\u5bcc\u53bf', 'level': 3 }
        }
      },
      '2724': {
        'id': 2724,
        'pid': 2670,
        'name': '\u4e3d\u6c5f\u5e02',
        'level': 2,
        'region': {
          '2725': { 'id': 2725, 'pid': 2724, 'name': '\u53e4\u57ce\u533a', 'level': 3 },
          '2726': { 'id': 2726, 'pid': 2724, 'name': '\u7389\u9f99\u7eb3\u897f\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2727': { 'id': 2727, 'pid': 2724, 'name': '\u6c38\u80dc\u53bf', 'level': 3 },
          '2728': { 'id': 2728, 'pid': 2724, 'name': '\u534e\u576a\u53bf', 'level': 3 },
          '2729': { 'id': 2729, 'pid': 2724, 'name': '\u5b81\u8497\u5f5d\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '2730': {
        'id': 2730, 'pid': 2670, 'name': '\u666e\u6d31\u5e02', 'level': 2, 'region': {
          '2731': { 'id': 2731, 'pid': 2730, 'name': '\u601d\u8305\u533a', 'level': 3 },
          '2732': {
            'id': 2732,
            'pid': 2730,
            'name': '\u5b81\u6d31\u54c8\u5c3c\u65cf\u5f5d\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2733': { 'id': 2733, 'pid': 2730, 'name': '\u58a8\u6c5f\u54c8\u5c3c\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2734': { 'id': 2734, 'pid': 2730, 'name': '\u666f\u4e1c\u5f5d\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2735': {
            'id': 2735,
            'pid': 2730,
            'name': '\u666f\u8c37\u50a3\u65cf\u5f5d\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2736': {
            'id': 2736,
            'pid': 2730,
            'name': '\u9547\u6c85\u5f5d\u65cf\u54c8\u5c3c\u65cf\u62c9\u795c\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2737': {
            'id': 2737,
            'pid': 2730,
            'name': '\u6c5f\u57ce\u54c8\u5c3c\u65cf\u5f5d\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2738': {
            'id': 2738,
            'pid': 2730,
            'name': '\u5b5f\u8fde\u50a3\u65cf\u62c9\u795c\u65cf\u4f64\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2739': { 'id': 2739, 'pid': 2730, 'name': '\u6f9c\u6ca7\u62c9\u795c\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2740': { 'id': 2740, 'pid': 2730, 'name': '\u897f\u76df\u4f64\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '2741': {
        'id': 2741,
        'pid': 2670,
        'name': '\u4e34\u6ca7\u5e02',
        'level': 2,
        'region': {
          '2742': { 'id': 2742, 'pid': 2741, 'name': '\u4e34\u7fd4\u533a', 'level': 3 },
          '2743': { 'id': 2743, 'pid': 2741, 'name': '\u51e4\u5e86\u53bf', 'level': 3 },
          '2744': { 'id': 2744, 'pid': 2741, 'name': '\u4e91\u53bf', 'level': 3 },
          '2745': { 'id': 2745, 'pid': 2741, 'name': '\u6c38\u5fb7\u53bf', 'level': 3 },
          '2746': { 'id': 2746, 'pid': 2741, 'name': '\u9547\u5eb7\u53bf', 'level': 3 },
          '2747': {
            'id': 2747,
            'pid': 2741,
            'name': '\u53cc\u6c5f\u62c9\u795c\u65cf\u4f64\u65cf\u5e03\u6717\u65cf\u50a3\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2748': {
            'id': 2748,
            'pid': 2741,
            'name': '\u803f\u9a6c\u50a3\u65cf\u4f64\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2749': { 'id': 2749, 'pid': 2741, 'name': '\u6ca7\u6e90\u4f64\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '2750': {
        'id': 2750,
        'pid': 2670,
        'name': '\u695a\u96c4\u5f5d\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '2751': { 'id': 2751, 'pid': 2750, 'name': '\u695a\u96c4\u5e02', 'level': 3 },
          '2752': { 'id': 2752, 'pid': 2750, 'name': '\u53cc\u67cf\u53bf', 'level': 3 },
          '2753': { 'id': 2753, 'pid': 2750, 'name': '\u725f\u5b9a\u53bf', 'level': 3 },
          '2754': { 'id': 2754, 'pid': 2750, 'name': '\u5357\u534e\u53bf', 'level': 3 },
          '2755': { 'id': 2755, 'pid': 2750, 'name': '\u59da\u5b89\u53bf', 'level': 3 },
          '2756': { 'id': 2756, 'pid': 2750, 'name': '\u5927\u59da\u53bf', 'level': 3 },
          '2757': { 'id': 2757, 'pid': 2750, 'name': '\u6c38\u4ec1\u53bf', 'level': 3 },
          '2758': { 'id': 2758, 'pid': 2750, 'name': '\u5143\u8c0b\u53bf', 'level': 3 },
          '2759': { 'id': 2759, 'pid': 2750, 'name': '\u6b66\u5b9a\u53bf', 'level': 3 },
          '2760': { 'id': 2760, 'pid': 2750, 'name': '\u7984\u4e30\u53bf', 'level': 3 }
        }
      },
      '2761': {
        'id': 2761,
        'pid': 2670,
        'name': '\u7ea2\u6cb3\u54c8\u5c3c\u65cf\u5f5d\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '2762': { 'id': 2762, 'pid': 2761, 'name': '\u4e2a\u65e7\u5e02', 'level': 3 },
          '2763': { 'id': 2763, 'pid': 2761, 'name': '\u5f00\u8fdc\u5e02', 'level': 3 },
          '2764': { 'id': 2764, 'pid': 2761, 'name': '\u8499\u81ea\u5e02', 'level': 3 },
          '2765': { 'id': 2765, 'pid': 2761, 'name': '\u5f25\u52d2\u5e02', 'level': 3 },
          '2766': { 'id': 2766, 'pid': 2761, 'name': '\u5c4f\u8fb9\u82d7\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2767': { 'id': 2767, 'pid': 2761, 'name': '\u5efa\u6c34\u53bf', 'level': 3 },
          '2768': { 'id': 2768, 'pid': 2761, 'name': '\u77f3\u5c4f\u53bf', 'level': 3 },
          '2769': { 'id': 2769, 'pid': 2761, 'name': '\u6cf8\u897f\u53bf', 'level': 3 },
          '2770': { 'id': 2770, 'pid': 2761, 'name': '\u5143\u9633\u53bf', 'level': 3 },
          '2771': { 'id': 2771, 'pid': 2761, 'name': '\u7ea2\u6cb3\u53bf', 'level': 3 },
          '2772': {
            'id': 2772,
            'pid': 2761,
            'name': '\u91d1\u5e73\u82d7\u65cf\u7476\u65cf\u50a3\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2773': { 'id': 2773, 'pid': 2761, 'name': '\u7eff\u6625\u53bf', 'level': 3 },
          '2774': { 'id': 2774, 'pid': 2761, 'name': '\u6cb3\u53e3\u7476\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '2775': {
        'id': 2775,
        'pid': 2670,
        'name': '\u6587\u5c71\u58ee\u65cf\u82d7\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '2776': { 'id': 2776, 'pid': 2775, 'name': '\u6587\u5c71\u5e02', 'level': 3 },
          '2777': { 'id': 2777, 'pid': 2775, 'name': '\u781a\u5c71\u53bf', 'level': 3 },
          '2778': { 'id': 2778, 'pid': 2775, 'name': '\u897f\u7574\u53bf', 'level': 3 },
          '2779': { 'id': 2779, 'pid': 2775, 'name': '\u9ebb\u6817\u5761\u53bf', 'level': 3 },
          '2780': { 'id': 2780, 'pid': 2775, 'name': '\u9a6c\u5173\u53bf', 'level': 3 },
          '2781': { 'id': 2781, 'pid': 2775, 'name': '\u4e18\u5317\u53bf', 'level': 3 },
          '2782': { 'id': 2782, 'pid': 2775, 'name': '\u5e7f\u5357\u53bf', 'level': 3 },
          '2783': { 'id': 2783, 'pid': 2775, 'name': '\u5bcc\u5b81\u53bf', 'level': 3 }
        }
      },
      '2784': {
        'id': 2784,
        'pid': 2670,
        'name': '\u897f\u53cc\u7248\u7eb3\u50a3\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '2785': { 'id': 2785, 'pid': 2784, 'name': '\u666f\u6d2a\u5e02', 'level': 3 },
          '2786': { 'id': 2786, 'pid': 2784, 'name': '\u52d0\u6d77\u53bf', 'level': 3 },
          '2787': { 'id': 2787, 'pid': 2784, 'name': '\u52d0\u814a\u53bf', 'level': 3 }
        }
      },
      '2788': {
        'id': 2788,
        'pid': 2670,
        'name': '\u5927\u7406\u767d\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '2789': { 'id': 2789, 'pid': 2788, 'name': '\u5927\u7406\u5e02', 'level': 3 },
          '2790': { 'id': 2790, 'pid': 2788, 'name': '\u6f3e\u6fde\u5f5d\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2791': { 'id': 2791, 'pid': 2788, 'name': '\u7965\u4e91\u53bf', 'level': 3 },
          '2792': { 'id': 2792, 'pid': 2788, 'name': '\u5bbe\u5ddd\u53bf', 'level': 3 },
          '2793': { 'id': 2793, 'pid': 2788, 'name': '\u5f25\u6e21\u53bf', 'level': 3 },
          '2794': { 'id': 2794, 'pid': 2788, 'name': '\u5357\u6da7\u5f5d\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '2795': {
            'id': 2795,
            'pid': 2788,
            'name': '\u5dcd\u5c71\u5f5d\u65cf\u56de\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2796': { 'id': 2796, 'pid': 2788, 'name': '\u6c38\u5e73\u53bf', 'level': 3 },
          '2797': { 'id': 2797, 'pid': 2788, 'name': '\u4e91\u9f99\u53bf', 'level': 3 },
          '2798': { 'id': 2798, 'pid': 2788, 'name': '\u6d31\u6e90\u53bf', 'level': 3 },
          '2799': { 'id': 2799, 'pid': 2788, 'name': '\u5251\u5ddd\u53bf', 'level': 3 },
          '2800': { 'id': 2800, 'pid': 2788, 'name': '\u9e64\u5e86\u53bf', 'level': 3 }
        }
      },
      '2801': {
        'id': 2801,
        'pid': 2670,
        'name': '\u5fb7\u5b8f\u50a3\u65cf\u666f\u9887\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '2802': { 'id': 2802, 'pid': 2801, 'name': '\u745e\u4e3d\u5e02', 'level': 3 },
          '2803': { 'id': 2803, 'pid': 2801, 'name': '\u8292\u5e02', 'level': 3 },
          '2804': { 'id': 2804, 'pid': 2801, 'name': '\u6881\u6cb3\u53bf', 'level': 3 },
          '2805': { 'id': 2805, 'pid': 2801, 'name': '\u76c8\u6c5f\u53bf', 'level': 3 },
          '2806': { 'id': 2806, 'pid': 2801, 'name': '\u9647\u5ddd\u53bf', 'level': 3 }
        }
      },
      '2807': {
        'id': 2807,
        'pid': 2670,
        'name': '\u6012\u6c5f\u5088\u50f3\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '2808': { 'id': 2808, 'pid': 2807, 'name': '\u6cf8\u6c34\u53bf', 'level': 3 },
          '2809': { 'id': 2809, 'pid': 2807, 'name': '\u798f\u8d21\u53bf', 'level': 3 },
          '2810': {
            'id': 2810,
            'pid': 2807,
            'name': '\u8d21\u5c71\u72ec\u9f99\u65cf\u6012\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '2811': {
            'id': 2811,
            'pid': 2807,
            'name': '\u5170\u576a\u767d\u65cf\u666e\u7c73\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          }
        }
      },
      '2812': {
        'id': 2812,
        'pid': 2670,
        'name': '\u8fea\u5e86\u85cf\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '2813': { 'id': 2813, 'pid': 2812, 'name': '\u9999\u683c\u91cc\u62c9\u5e02', 'level': 3 },
          '2814': { 'id': 2814, 'pid': 2812, 'name': '\u5fb7\u94a6\u53bf', 'level': 3 },
          '2815': { 'id': 2815, 'pid': 2812, 'name': '\u7ef4\u897f\u5088\u50f3\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      }
    }
  },
  '2816': {
    'id': 2816, 'pid': 0, 'name': '\u897f\u85cf\u81ea\u6cbb\u533a', 'level': 1, 'city': {
      '2817': {
        'id': 2817,
        'pid': 2816,
        'name': '\u62c9\u8428\u5e02',
        'level': 2,
        'region': {
          '2818': { 'id': 2818, 'pid': 2817, 'name': '\u57ce\u5173\u533a', 'level': 3 },
          '2819': { 'id': 2819, 'pid': 2817, 'name': '\u6797\u5468\u53bf', 'level': 3 },
          '2820': { 'id': 2820, 'pid': 2817, 'name': '\u5f53\u96c4\u53bf', 'level': 3 },
          '2821': { 'id': 2821, 'pid': 2817, 'name': '\u5c3c\u6728\u53bf', 'level': 3 },
          '2822': { 'id': 2822, 'pid': 2817, 'name': '\u66f2\u6c34\u53bf', 'level': 3 },
          '2823': { 'id': 2823, 'pid': 2817, 'name': '\u5806\u9f99\u5fb7\u5e86\u53bf', 'level': 3 },
          '2824': { 'id': 2824, 'pid': 2817, 'name': '\u8fbe\u5b5c\u53bf', 'level': 3 },
          '2825': { 'id': 2825, 'pid': 2817, 'name': '\u58a8\u7af9\u5de5\u5361\u53bf', 'level': 3 }
        }
      },
      '2826': {
        'id': 2826, 'pid': 2816, 'name': '\u65e5\u5580\u5219\u5e02', 'level': 2, 'region': {
          '2827': { 'id': 2827, 'pid': 2826, 'name': '\u6851\u73e0\u5b5c\u533a', 'level': 3 },
          '2828': { 'id': 2828, 'pid': 2826, 'name': '\u5357\u6728\u6797\u53bf', 'level': 3 },
          '2829': { 'id': 2829, 'pid': 2826, 'name': '\u6c5f\u5b5c\u53bf', 'level': 3 },
          '2830': { 'id': 2830, 'pid': 2826, 'name': '\u5b9a\u65e5\u53bf', 'level': 3 },
          '2831': { 'id': 2831, 'pid': 2826, 'name': '\u8428\u8fe6\u53bf', 'level': 3 },
          '2832': { 'id': 2832, 'pid': 2826, 'name': '\u62c9\u5b5c\u53bf', 'level': 3 },
          '2833': { 'id': 2833, 'pid': 2826, 'name': '\u6602\u4ec1\u53bf', 'level': 3 },
          '2834': { 'id': 2834, 'pid': 2826, 'name': '\u8c22\u901a\u95e8\u53bf', 'level': 3 },
          '2835': { 'id': 2835, 'pid': 2826, 'name': '\u767d\u6717\u53bf', 'level': 3 },
          '2836': { 'id': 2836, 'pid': 2826, 'name': '\u4ec1\u5e03\u53bf', 'level': 3 },
          '2837': { 'id': 2837, 'pid': 2826, 'name': '\u5eb7\u9a6c\u53bf', 'level': 3 },
          '2838': { 'id': 2838, 'pid': 2826, 'name': '\u5b9a\u7ed3\u53bf', 'level': 3 },
          '2839': { 'id': 2839, 'pid': 2826, 'name': '\u4ef2\u5df4\u53bf', 'level': 3 },
          '2840': { 'id': 2840, 'pid': 2826, 'name': '\u4e9a\u4e1c\u53bf', 'level': 3 },
          '2841': { 'id': 2841, 'pid': 2826, 'name': '\u5409\u9686\u53bf', 'level': 3 },
          '2842': { 'id': 2842, 'pid': 2826, 'name': '\u8042\u62c9\u6728\u53bf', 'level': 3 },
          '2843': { 'id': 2843, 'pid': 2826, 'name': '\u8428\u560e\u53bf', 'level': 3 },
          '2844': { 'id': 2844, 'pid': 2826, 'name': '\u5c97\u5df4\u53bf', 'level': 3 }
        }
      },
      '2845': {
        'id': 2845,
        'pid': 2816,
        'name': '\u660c\u90fd\u5e02',
        'level': 2,
        'region': {
          '2846': { 'id': 2846, 'pid': 2845, 'name': '\u5361\u82e5\u533a', 'level': 3 },
          '2847': { 'id': 2847, 'pid': 2845, 'name': '\u6c5f\u8fbe\u53bf', 'level': 3 },
          '2848': { 'id': 2848, 'pid': 2845, 'name': '\u8d21\u89c9\u53bf', 'level': 3 },
          '2849': { 'id': 2849, 'pid': 2845, 'name': '\u7c7b\u4e4c\u9f50\u53bf', 'level': 3 },
          '2850': { 'id': 2850, 'pid': 2845, 'name': '\u4e01\u9752\u53bf', 'level': 3 },
          '2851': { 'id': 2851, 'pid': 2845, 'name': '\u5bdf\u96c5\u53bf', 'level': 3 },
          '2852': { 'id': 2852, 'pid': 2845, 'name': '\u516b\u5bbf\u53bf', 'level': 3 },
          '2853': { 'id': 2853, 'pid': 2845, 'name': '\u5de6\u8d21\u53bf', 'level': 3 },
          '2854': { 'id': 2854, 'pid': 2845, 'name': '\u8292\u5eb7\u53bf', 'level': 3 },
          '2855': { 'id': 2855, 'pid': 2845, 'name': '\u6d1b\u9686\u53bf', 'level': 3 },
          '2856': { 'id': 2856, 'pid': 2845, 'name': '\u8fb9\u575d\u53bf', 'level': 3 }
        }
      },
      '2857': {
        'id': 2857,
        'pid': 2816,
        'name': '\u5c71\u5357\u5730\u533a',
        'level': 2,
        'region': {
          '2858': { 'id': 2858, 'pid': 2857, 'name': '\u4e43\u4e1c\u53bf', 'level': 3 },
          '2859': { 'id': 2859, 'pid': 2857, 'name': '\u624e\u56ca\u53bf', 'level': 3 },
          '2860': { 'id': 2860, 'pid': 2857, 'name': '\u8d21\u560e\u53bf', 'level': 3 },
          '2861': { 'id': 2861, 'pid': 2857, 'name': '\u6851\u65e5\u53bf', 'level': 3 },
          '2862': { 'id': 2862, 'pid': 2857, 'name': '\u743c\u7ed3\u53bf', 'level': 3 },
          '2863': { 'id': 2863, 'pid': 2857, 'name': '\u66f2\u677e\u53bf', 'level': 3 },
          '2864': { 'id': 2864, 'pid': 2857, 'name': '\u63aa\u7f8e\u53bf', 'level': 3 },
          '2865': { 'id': 2865, 'pid': 2857, 'name': '\u6d1b\u624e\u53bf', 'level': 3 },
          '2866': { 'id': 2866, 'pid': 2857, 'name': '\u52a0\u67e5\u53bf', 'level': 3 },
          '2867': { 'id': 2867, 'pid': 2857, 'name': '\u9686\u5b50\u53bf', 'level': 3 },
          '2868': { 'id': 2868, 'pid': 2857, 'name': '\u9519\u90a3\u53bf', 'level': 3 },
          '2869': { 'id': 2869, 'pid': 2857, 'name': '\u6d6a\u5361\u5b50\u53bf', 'level': 3 }
        }
      },
      '2870': {
        'id': 2870,
        'pid': 2816,
        'name': '\u90a3\u66f2\u5730\u533a',
        'level': 2,
        'region': {
          '2871': { 'id': 2871, 'pid': 2870, 'name': '\u90a3\u66f2\u53bf', 'level': 3 },
          '2872': { 'id': 2872, 'pid': 2870, 'name': '\u5609\u9ece\u53bf', 'level': 3 },
          '2873': { 'id': 2873, 'pid': 2870, 'name': '\u6bd4\u5982\u53bf', 'level': 3 },
          '2874': { 'id': 2874, 'pid': 2870, 'name': '\u8042\u8363\u53bf', 'level': 3 },
          '2875': { 'id': 2875, 'pid': 2870, 'name': '\u5b89\u591a\u53bf', 'level': 3 },
          '2876': { 'id': 2876, 'pid': 2870, 'name': '\u7533\u624e\u53bf', 'level': 3 },
          '2877': { 'id': 2877, 'pid': 2870, 'name': '\u7d22\u53bf', 'level': 3 },
          '2878': { 'id': 2878, 'pid': 2870, 'name': '\u73ed\u6208\u53bf', 'level': 3 },
          '2879': { 'id': 2879, 'pid': 2870, 'name': '\u5df4\u9752\u53bf', 'level': 3 },
          '2880': { 'id': 2880, 'pid': 2870, 'name': '\u5c3c\u739b\u53bf', 'level': 3 },
          '2881': { 'id': 2881, 'pid': 2870, 'name': '\u53cc\u6e56\u53bf', 'level': 3 }
        }
      },
      '2882': {
        'id': 2882,
        'pid': 2816,
        'name': '\u963f\u91cc\u5730\u533a',
        'level': 2,
        'region': {
          '2883': { 'id': 2883, 'pid': 2882, 'name': '\u666e\u5170\u53bf', 'level': 3 },
          '2884': { 'id': 2884, 'pid': 2882, 'name': '\u672d\u8fbe\u53bf', 'level': 3 },
          '2885': { 'id': 2885, 'pid': 2882, 'name': '\u5676\u5c14\u53bf', 'level': 3 },
          '2886': { 'id': 2886, 'pid': 2882, 'name': '\u65e5\u571f\u53bf', 'level': 3 },
          '2887': { 'id': 2887, 'pid': 2882, 'name': '\u9769\u5409\u53bf', 'level': 3 },
          '2888': { 'id': 2888, 'pid': 2882, 'name': '\u6539\u5219\u53bf', 'level': 3 },
          '2889': { 'id': 2889, 'pid': 2882, 'name': '\u63aa\u52e4\u53bf', 'level': 3 }
        }
      },
      '2890': {
        'id': 2890,
        'pid': 2816,
        'name': '\u6797\u829d\u5730\u533a',
        'level': 2,
        'region': {
          '2891': { 'id': 2891, 'pid': 2890, 'name': '\u6797\u829d\u53bf', 'level': 3 },
          '2892': { 'id': 2892, 'pid': 2890, 'name': '\u5de5\u5e03\u6c5f\u8fbe\u53bf', 'level': 3 },
          '2893': { 'id': 2893, 'pid': 2890, 'name': '\u7c73\u6797\u53bf', 'level': 3 },
          '2894': { 'id': 2894, 'pid': 2890, 'name': '\u58a8\u8131\u53bf', 'level': 3 },
          '2895': { 'id': 2895, 'pid': 2890, 'name': '\u6ce2\u5bc6\u53bf', 'level': 3 },
          '2896': { 'id': 2896, 'pid': 2890, 'name': '\u5bdf\u9685\u53bf', 'level': 3 },
          '2897': { 'id': 2897, 'pid': 2890, 'name': '\u6717\u53bf', 'level': 3 }
        }
      }
    }
  },
  '2898': {
    'id': 2898, 'pid': 0, 'name': '\u9655\u897f\u7701', 'level': 1, 'city': {
      '2899': {
        'id': 2899,
        'pid': 2898,
        'name': '\u897f\u5b89\u5e02',
        'level': 2,
        'region': {
          '2900': { 'id': 2900, 'pid': 2899, 'name': '\u65b0\u57ce\u533a', 'level': 3 },
          '2901': { 'id': 2901, 'pid': 2899, 'name': '\u7891\u6797\u533a', 'level': 3 },
          '2902': { 'id': 2902, 'pid': 2899, 'name': '\u83b2\u6e56\u533a', 'level': 3 },
          '2903': { 'id': 2903, 'pid': 2899, 'name': '\u705e\u6865\u533a', 'level': 3 },
          '2904': { 'id': 2904, 'pid': 2899, 'name': '\u672a\u592e\u533a', 'level': 3 },
          '2905': { 'id': 2905, 'pid': 2899, 'name': '\u96c1\u5854\u533a', 'level': 3 },
          '2906': { 'id': 2906, 'pid': 2899, 'name': '\u960e\u826f\u533a', 'level': 3 },
          '2907': { 'id': 2907, 'pid': 2899, 'name': '\u4e34\u6f7c\u533a', 'level': 3 },
          '2908': { 'id': 2908, 'pid': 2899, 'name': '\u957f\u5b89\u533a', 'level': 3 },
          '2909': { 'id': 2909, 'pid': 2899, 'name': '\u84dd\u7530\u53bf', 'level': 3 },
          '2910': { 'id': 2910, 'pid': 2899, 'name': '\u5468\u81f3\u53bf', 'level': 3 },
          '2911': { 'id': 2911, 'pid': 2899, 'name': '\u6237\u53bf', 'level': 3 },
          '2912': { 'id': 2912, 'pid': 2899, 'name': '\u9ad8\u9675\u533a', 'level': 3 }
        }
      },
      '2913': {
        'id': 2913,
        'pid': 2898,
        'name': '\u94dc\u5ddd\u5e02',
        'level': 2,
        'region': {
          '2914': { 'id': 2914, 'pid': 2913, 'name': '\u738b\u76ca\u533a', 'level': 3 },
          '2915': { 'id': 2915, 'pid': 2913, 'name': '\u5370\u53f0\u533a', 'level': 3 },
          '2916': { 'id': 2916, 'pid': 2913, 'name': '\u8000\u5dde\u533a', 'level': 3 },
          '2917': { 'id': 2917, 'pid': 2913, 'name': '\u5b9c\u541b\u53bf', 'level': 3 }
        }
      },
      '2918': {
        'id': 2918,
        'pid': 2898,
        'name': '\u5b9d\u9e21\u5e02',
        'level': 2,
        'region': {
          '2919': { 'id': 2919, 'pid': 2918, 'name': '\u6e2d\u6ee8\u533a', 'level': 3 },
          '2920': { 'id': 2920, 'pid': 2918, 'name': '\u91d1\u53f0\u533a', 'level': 3 },
          '2921': { 'id': 2921, 'pid': 2918, 'name': '\u9648\u4ed3\u533a', 'level': 3 },
          '2922': { 'id': 2922, 'pid': 2918, 'name': '\u51e4\u7fd4\u53bf', 'level': 3 },
          '2923': { 'id': 2923, 'pid': 2918, 'name': '\u5c90\u5c71\u53bf', 'level': 3 },
          '2924': { 'id': 2924, 'pid': 2918, 'name': '\u6276\u98ce\u53bf', 'level': 3 },
          '2925': { 'id': 2925, 'pid': 2918, 'name': '\u7709\u53bf', 'level': 3 },
          '2926': { 'id': 2926, 'pid': 2918, 'name': '\u9647\u53bf', 'level': 3 },
          '2927': { 'id': 2927, 'pid': 2918, 'name': '\u5343\u9633\u53bf', 'level': 3 },
          '2928': { 'id': 2928, 'pid': 2918, 'name': '\u9e9f\u6e38\u53bf', 'level': 3 },
          '2929': { 'id': 2929, 'pid': 2918, 'name': '\u51e4\u53bf', 'level': 3 },
          '2930': { 'id': 2930, 'pid': 2918, 'name': '\u592a\u767d\u53bf', 'level': 3 }
        }
      },
      '2931': {
        'id': 2931,
        'pid': 2898,
        'name': '\u54b8\u9633\u5e02',
        'level': 2,
        'region': {
          '2932': { 'id': 2932, 'pid': 2931, 'name': '\u79e6\u90fd\u533a', 'level': 3 },
          '2933': { 'id': 2933, 'pid': 2931, 'name': '\u6768\u9675\u533a', 'level': 3 },
          '2934': { 'id': 2934, 'pid': 2931, 'name': '\u6e2d\u57ce\u533a', 'level': 3 },
          '2935': { 'id': 2935, 'pid': 2931, 'name': '\u4e09\u539f\u53bf', 'level': 3 },
          '2936': { 'id': 2936, 'pid': 2931, 'name': '\u6cfe\u9633\u53bf', 'level': 3 },
          '2937': { 'id': 2937, 'pid': 2931, 'name': '\u4e7e\u53bf', 'level': 3 },
          '2938': { 'id': 2938, 'pid': 2931, 'name': '\u793c\u6cc9\u53bf', 'level': 3 },
          '2939': { 'id': 2939, 'pid': 2931, 'name': '\u6c38\u5bff\u53bf', 'level': 3 },
          '2940': { 'id': 2940, 'pid': 2931, 'name': '\u5f6c\u53bf', 'level': 3 },
          '2941': { 'id': 2941, 'pid': 2931, 'name': '\u957f\u6b66\u53bf', 'level': 3 },
          '2942': { 'id': 2942, 'pid': 2931, 'name': '\u65ec\u9091\u53bf', 'level': 3 },
          '2943': { 'id': 2943, 'pid': 2931, 'name': '\u6df3\u5316\u53bf', 'level': 3 },
          '2944': { 'id': 2944, 'pid': 2931, 'name': '\u6b66\u529f\u53bf', 'level': 3 },
          '2945': { 'id': 2945, 'pid': 2931, 'name': '\u5174\u5e73\u5e02', 'level': 3 }
        }
      },
      '2946': {
        'id': 2946,
        'pid': 2898,
        'name': '\u6e2d\u5357\u5e02',
        'level': 2,
        'region': {
          '2947': { 'id': 2947, 'pid': 2946, 'name': '\u4e34\u6e2d\u533a', 'level': 3 },
          '2948': { 'id': 2948, 'pid': 2946, 'name': '\u534e\u53bf', 'level': 3 },
          '2949': { 'id': 2949, 'pid': 2946, 'name': '\u6f7c\u5173\u53bf', 'level': 3 },
          '2950': { 'id': 2950, 'pid': 2946, 'name': '\u5927\u8354\u53bf', 'level': 3 },
          '2951': { 'id': 2951, 'pid': 2946, 'name': '\u5408\u9633\u53bf', 'level': 3 },
          '2952': { 'id': 2952, 'pid': 2946, 'name': '\u6f84\u57ce\u53bf', 'level': 3 },
          '2953': { 'id': 2953, 'pid': 2946, 'name': '\u84b2\u57ce\u53bf', 'level': 3 },
          '2954': { 'id': 2954, 'pid': 2946, 'name': '\u767d\u6c34\u53bf', 'level': 3 },
          '2955': { 'id': 2955, 'pid': 2946, 'name': '\u5bcc\u5e73\u53bf', 'level': 3 },
          '2956': { 'id': 2956, 'pid': 2946, 'name': '\u97e9\u57ce\u5e02', 'level': 3 },
          '2957': { 'id': 2957, 'pid': 2946, 'name': '\u534e\u9634\u5e02', 'level': 3 }
        }
      },
      '2958': {
        'id': 2958,
        'pid': 2898,
        'name': '\u5ef6\u5b89\u5e02',
        'level': 2,
        'region': {
          '2959': { 'id': 2959, 'pid': 2958, 'name': '\u5b9d\u5854\u533a', 'level': 3 },
          '2960': { 'id': 2960, 'pid': 2958, 'name': '\u5ef6\u957f\u53bf', 'level': 3 },
          '2961': { 'id': 2961, 'pid': 2958, 'name': '\u5ef6\u5ddd\u53bf', 'level': 3 },
          '2962': { 'id': 2962, 'pid': 2958, 'name': '\u5b50\u957f\u53bf', 'level': 3 },
          '2963': { 'id': 2963, 'pid': 2958, 'name': '\u5b89\u585e\u53bf', 'level': 3 },
          '2964': { 'id': 2964, 'pid': 2958, 'name': '\u5fd7\u4e39\u53bf', 'level': 3 },
          '2965': { 'id': 2965, 'pid': 2958, 'name': '\u5434\u8d77\u53bf', 'level': 3 },
          '2966': { 'id': 2966, 'pid': 2958, 'name': '\u7518\u6cc9\u53bf', 'level': 3 },
          '2967': { 'id': 2967, 'pid': 2958, 'name': '\u5bcc\u53bf', 'level': 3 },
          '2968': { 'id': 2968, 'pid': 2958, 'name': '\u6d1b\u5ddd\u53bf', 'level': 3 },
          '2969': { 'id': 2969, 'pid': 2958, 'name': '\u5b9c\u5ddd\u53bf', 'level': 3 },
          '2970': { 'id': 2970, 'pid': 2958, 'name': '\u9ec4\u9f99\u53bf', 'level': 3 },
          '2971': { 'id': 2971, 'pid': 2958, 'name': '\u9ec4\u9675\u53bf', 'level': 3 }
        }
      },
      '2972': {
        'id': 2972,
        'pid': 2898,
        'name': '\u6c49\u4e2d\u5e02',
        'level': 2,
        'region': {
          '2973': { 'id': 2973, 'pid': 2972, 'name': '\u6c49\u53f0\u533a', 'level': 3 },
          '2974': { 'id': 2974, 'pid': 2972, 'name': '\u5357\u90d1\u53bf', 'level': 3 },
          '2975': { 'id': 2975, 'pid': 2972, 'name': '\u57ce\u56fa\u53bf', 'level': 3 },
          '2976': { 'id': 2976, 'pid': 2972, 'name': '\u6d0b\u53bf', 'level': 3 },
          '2977': { 'id': 2977, 'pid': 2972, 'name': '\u897f\u4e61\u53bf', 'level': 3 },
          '2978': { 'id': 2978, 'pid': 2972, 'name': '\u52c9\u53bf', 'level': 3 },
          '2979': { 'id': 2979, 'pid': 2972, 'name': '\u5b81\u5f3a\u53bf', 'level': 3 },
          '2980': { 'id': 2980, 'pid': 2972, 'name': '\u7565\u9633\u53bf', 'level': 3 },
          '2981': { 'id': 2981, 'pid': 2972, 'name': '\u9547\u5df4\u53bf', 'level': 3 },
          '2982': { 'id': 2982, 'pid': 2972, 'name': '\u7559\u575d\u53bf', 'level': 3 },
          '2983': { 'id': 2983, 'pid': 2972, 'name': '\u4f5b\u576a\u53bf', 'level': 3 }
        }
      },
      '2984': {
        'id': 2984,
        'pid': 2898,
        'name': '\u6986\u6797\u5e02',
        'level': 2,
        'region': {
          '2985': { 'id': 2985, 'pid': 2984, 'name': '\u6986\u9633\u533a', 'level': 3 },
          '2986': { 'id': 2986, 'pid': 2984, 'name': '\u795e\u6728\u53bf', 'level': 3 },
          '2987': { 'id': 2987, 'pid': 2984, 'name': '\u5e9c\u8c37\u53bf', 'level': 3 },
          '2988': { 'id': 2988, 'pid': 2984, 'name': '\u6a2a\u5c71\u53bf', 'level': 3 },
          '2989': { 'id': 2989, 'pid': 2984, 'name': '\u9756\u8fb9\u53bf', 'level': 3 },
          '2990': { 'id': 2990, 'pid': 2984, 'name': '\u5b9a\u8fb9\u53bf', 'level': 3 },
          '2991': { 'id': 2991, 'pid': 2984, 'name': '\u7ee5\u5fb7\u53bf', 'level': 3 },
          '2992': { 'id': 2992, 'pid': 2984, 'name': '\u7c73\u8102\u53bf', 'level': 3 },
          '2993': { 'id': 2993, 'pid': 2984, 'name': '\u4f73\u53bf', 'level': 3 },
          '2994': { 'id': 2994, 'pid': 2984, 'name': '\u5434\u5821\u53bf', 'level': 3 },
          '2995': { 'id': 2995, 'pid': 2984, 'name': '\u6e05\u6da7\u53bf', 'level': 3 },
          '2996': { 'id': 2996, 'pid': 2984, 'name': '\u5b50\u6d32\u53bf', 'level': 3 }
        }
      },
      '2997': {
        'id': 2997,
        'pid': 2898,
        'name': '\u5b89\u5eb7\u5e02',
        'level': 2,
        'region': {
          '2998': { 'id': 2998, 'pid': 2997, 'name': '\u6c49\u6ee8\u533a', 'level': 3 },
          '2999': { 'id': 2999, 'pid': 2997, 'name': '\u6c49\u9634\u53bf', 'level': 3 },
          '3000': { 'id': 3000, 'pid': 2997, 'name': '\u77f3\u6cc9\u53bf', 'level': 3 },
          '3001': { 'id': 3001, 'pid': 2997, 'name': '\u5b81\u9655\u53bf', 'level': 3 },
          '3002': { 'id': 3002, 'pid': 2997, 'name': '\u7d2b\u9633\u53bf', 'level': 3 },
          '3003': { 'id': 3003, 'pid': 2997, 'name': '\u5c9a\u768b\u53bf', 'level': 3 },
          '3004': { 'id': 3004, 'pid': 2997, 'name': '\u5e73\u5229\u53bf', 'level': 3 },
          '3005': { 'id': 3005, 'pid': 2997, 'name': '\u9547\u576a\u53bf', 'level': 3 },
          '3006': { 'id': 3006, 'pid': 2997, 'name': '\u65ec\u9633\u53bf', 'level': 3 },
          '3007': { 'id': 3007, 'pid': 2997, 'name': '\u767d\u6cb3\u53bf', 'level': 3 }
        }
      },
      '3008': {
        'id': 3008,
        'pid': 2898,
        'name': '\u5546\u6d1b\u5e02',
        'level': 2,
        'region': {
          '3009': { 'id': 3009, 'pid': 3008, 'name': '\u5546\u5dde\u533a', 'level': 3 },
          '3010': { 'id': 3010, 'pid': 3008, 'name': '\u6d1b\u5357\u53bf', 'level': 3 },
          '3011': { 'id': 3011, 'pid': 3008, 'name': '\u4e39\u51e4\u53bf', 'level': 3 },
          '3012': { 'id': 3012, 'pid': 3008, 'name': '\u5546\u5357\u53bf', 'level': 3 },
          '3013': { 'id': 3013, 'pid': 3008, 'name': '\u5c71\u9633\u53bf', 'level': 3 },
          '3014': { 'id': 3014, 'pid': 3008, 'name': '\u9547\u5b89\u53bf', 'level': 3 },
          '3015': { 'id': 3015, 'pid': 3008, 'name': '\u67de\u6c34\u53bf', 'level': 3 }
        }
      },
      '3016': {
        'id': 3016,
        'pid': 2898,
        'name': '\u897f\u54b8\u65b0\u533a',
        'level': 2,
        'region': {
          '3017': { 'id': 3017, 'pid': 3016, 'name': '\u7a7a\u6e2f\u65b0\u57ce', 'level': 3 },
          '3018': { 'id': 3018, 'pid': 3016, 'name': '\u6ca3\u4e1c\u65b0\u57ce', 'level': 3 },
          '3019': { 'id': 3019, 'pid': 3016, 'name': '\u79e6\u6c49\u65b0\u57ce', 'level': 3 },
          '3020': { 'id': 3020, 'pid': 3016, 'name': '\u6ca3\u897f\u65b0\u57ce', 'level': 3 },
          '3021': { 'id': 3021, 'pid': 3016, 'name': '\u6cfe\u6cb3\u65b0\u57ce', 'level': 3 }
        }
      }
    }
  },
  '3022': {
    'id': 3022, 'pid': 0, 'name': '\u7518\u8083\u7701', 'level': 1, 'city': {
      '3023': {
        'id': 3023,
        'pid': 3022,
        'name': '\u5170\u5dde\u5e02',
        'level': 2,
        'region': {
          '3024': { 'id': 3024, 'pid': 3023, 'name': '\u57ce\u5173\u533a', 'level': 3 },
          '3025': { 'id': 3025, 'pid': 3023, 'name': '\u4e03\u91cc\u6cb3\u533a', 'level': 3 },
          '3026': { 'id': 3026, 'pid': 3023, 'name': '\u897f\u56fa\u533a', 'level': 3 },
          '3027': { 'id': 3027, 'pid': 3023, 'name': '\u5b89\u5b81\u533a', 'level': 3 },
          '3028': { 'id': 3028, 'pid': 3023, 'name': '\u7ea2\u53e4\u533a', 'level': 3 },
          '3029': { 'id': 3029, 'pid': 3023, 'name': '\u6c38\u767b\u53bf', 'level': 3 },
          '3030': { 'id': 3030, 'pid': 3023, 'name': '\u768b\u5170\u53bf', 'level': 3 },
          '3031': { 'id': 3031, 'pid': 3023, 'name': '\u6986\u4e2d\u53bf', 'level': 3 }
        }
      },
      '3032': {
        'id': 3032,
        'pid': 3022,
        'name': '\u5609\u5cea\u5173\u5e02',
        'level': 2,
        'region': {
          '3033': { 'id': 3033, 'pid': 3032, 'name': '\u96c4\u5173\u533a', 'level': 3 },
          '3034': { 'id': 3034, 'pid': 3032, 'name': '\u957f\u57ce\u533a', 'level': 3 },
          '3035': { 'id': 3035, 'pid': 3032, 'name': '\u955c\u94c1\u533a', 'level': 3 }
        }
      },
      '3036': {
        'id': 3036,
        'pid': 3022,
        'name': '\u91d1\u660c\u5e02',
        'level': 2,
        'region': {
          '3037': { 'id': 3037, 'pid': 3036, 'name': '\u91d1\u5ddd\u533a', 'level': 3 },
          '3038': { 'id': 3038, 'pid': 3036, 'name': '\u6c38\u660c\u53bf', 'level': 3 }
        }
      },
      '3039': {
        'id': 3039,
        'pid': 3022,
        'name': '\u767d\u94f6\u5e02',
        'level': 2,
        'region': {
          '3040': { 'id': 3040, 'pid': 3039, 'name': '\u767d\u94f6\u533a', 'level': 3 },
          '3041': { 'id': 3041, 'pid': 3039, 'name': '\u5e73\u5ddd\u533a', 'level': 3 },
          '3042': { 'id': 3042, 'pid': 3039, 'name': '\u9756\u8fdc\u53bf', 'level': 3 },
          '3043': { 'id': 3043, 'pid': 3039, 'name': '\u4f1a\u5b81\u53bf', 'level': 3 },
          '3044': { 'id': 3044, 'pid': 3039, 'name': '\u666f\u6cf0\u53bf', 'level': 3 }
        }
      },
      '3045': {
        'id': 3045,
        'pid': 3022,
        'name': '\u5929\u6c34\u5e02',
        'level': 2,
        'region': {
          '3046': { 'id': 3046, 'pid': 3045, 'name': '\u79e6\u5dde\u533a', 'level': 3 },
          '3047': { 'id': 3047, 'pid': 3045, 'name': '\u9ea6\u79ef\u533a', 'level': 3 },
          '3048': { 'id': 3048, 'pid': 3045, 'name': '\u6e05\u6c34\u53bf', 'level': 3 },
          '3049': { 'id': 3049, 'pid': 3045, 'name': '\u79e6\u5b89\u53bf', 'level': 3 },
          '3050': { 'id': 3050, 'pid': 3045, 'name': '\u7518\u8c37\u53bf', 'level': 3 },
          '3051': { 'id': 3051, 'pid': 3045, 'name': '\u6b66\u5c71\u53bf', 'level': 3 },
          '3052': { 'id': 3052, 'pid': 3045, 'name': '\u5f20\u5bb6\u5ddd\u56de\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '3053': {
        'id': 3053,
        'pid': 3022,
        'name': '\u6b66\u5a01\u5e02',
        'level': 2,
        'region': {
          '3054': { 'id': 3054, 'pid': 3053, 'name': '\u51c9\u5dde\u533a', 'level': 3 },
          '3055': { 'id': 3055, 'pid': 3053, 'name': '\u6c11\u52e4\u53bf', 'level': 3 },
          '3056': { 'id': 3056, 'pid': 3053, 'name': '\u53e4\u6d6a\u53bf', 'level': 3 },
          '3057': { 'id': 3057, 'pid': 3053, 'name': '\u5929\u795d\u85cf\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '3058': {
        'id': 3058,
        'pid': 3022,
        'name': '\u5f20\u6396\u5e02',
        'level': 2,
        'region': {
          '3059': { 'id': 3059, 'pid': 3058, 'name': '\u7518\u5dde\u533a', 'level': 3 },
          '3060': { 'id': 3060, 'pid': 3058, 'name': '\u8083\u5357\u88d5\u56fa\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '3061': { 'id': 3061, 'pid': 3058, 'name': '\u6c11\u4e50\u53bf', 'level': 3 },
          '3062': { 'id': 3062, 'pid': 3058, 'name': '\u4e34\u6cfd\u53bf', 'level': 3 },
          '3063': { 'id': 3063, 'pid': 3058, 'name': '\u9ad8\u53f0\u53bf', 'level': 3 },
          '3064': { 'id': 3064, 'pid': 3058, 'name': '\u5c71\u4e39\u53bf', 'level': 3 }
        }
      },
      '3065': {
        'id': 3065,
        'pid': 3022,
        'name': '\u5e73\u51c9\u5e02',
        'level': 2,
        'region': {
          '3066': { 'id': 3066, 'pid': 3065, 'name': '\u5d06\u5cd2\u533a', 'level': 3 },
          '3067': { 'id': 3067, 'pid': 3065, 'name': '\u6cfe\u5ddd\u53bf', 'level': 3 },
          '3068': { 'id': 3068, 'pid': 3065, 'name': '\u7075\u53f0\u53bf', 'level': 3 },
          '3069': { 'id': 3069, 'pid': 3065, 'name': '\u5d07\u4fe1\u53bf', 'level': 3 },
          '3070': { 'id': 3070, 'pid': 3065, 'name': '\u534e\u4ead\u53bf', 'level': 3 },
          '3071': { 'id': 3071, 'pid': 3065, 'name': '\u5e84\u6d6a\u53bf', 'level': 3 },
          '3072': { 'id': 3072, 'pid': 3065, 'name': '\u9759\u5b81\u53bf', 'level': 3 }
        }
      },
      '3073': {
        'id': 3073,
        'pid': 3022,
        'name': '\u9152\u6cc9\u5e02',
        'level': 2,
        'region': {
          '3074': { 'id': 3074, 'pid': 3073, 'name': '\u8083\u5dde\u533a', 'level': 3 },
          '3075': { 'id': 3075, 'pid': 3073, 'name': '\u91d1\u5854\u53bf', 'level': 3 },
          '3076': { 'id': 3076, 'pid': 3073, 'name': '\u74dc\u5dde\u53bf', 'level': 3 },
          '3077': { 'id': 3077, 'pid': 3073, 'name': '\u8083\u5317\u8499\u53e4\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '3078': {
            'id': 3078,
            'pid': 3073,
            'name': '\u963f\u514b\u585e\u54c8\u8428\u514b\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '3079': { 'id': 3079, 'pid': 3073, 'name': '\u7389\u95e8\u5e02', 'level': 3 },
          '3080': { 'id': 3080, 'pid': 3073, 'name': '\u6566\u714c\u5e02', 'level': 3 }
        }
      },
      '3081': {
        'id': 3081,
        'pid': 3022,
        'name': '\u5e86\u9633\u5e02',
        'level': 2,
        'region': {
          '3082': { 'id': 3082, 'pid': 3081, 'name': '\u897f\u5cf0\u533a', 'level': 3 },
          '3083': { 'id': 3083, 'pid': 3081, 'name': '\u5e86\u57ce\u53bf', 'level': 3 },
          '3084': { 'id': 3084, 'pid': 3081, 'name': '\u73af\u53bf', 'level': 3 },
          '3085': { 'id': 3085, 'pid': 3081, 'name': '\u534e\u6c60\u53bf', 'level': 3 },
          '3086': { 'id': 3086, 'pid': 3081, 'name': '\u5408\u6c34\u53bf', 'level': 3 },
          '3087': { 'id': 3087, 'pid': 3081, 'name': '\u6b63\u5b81\u53bf', 'level': 3 },
          '3088': { 'id': 3088, 'pid': 3081, 'name': '\u5b81\u53bf', 'level': 3 },
          '3089': { 'id': 3089, 'pid': 3081, 'name': '\u9547\u539f\u53bf', 'level': 3 }
        }
      },
      '3090': {
        'id': 3090,
        'pid': 3022,
        'name': '\u5b9a\u897f\u5e02',
        'level': 2,
        'region': {
          '3091': { 'id': 3091, 'pid': 3090, 'name': '\u5b89\u5b9a\u533a', 'level': 3 },
          '3092': { 'id': 3092, 'pid': 3090, 'name': '\u901a\u6e2d\u53bf', 'level': 3 },
          '3093': { 'id': 3093, 'pid': 3090, 'name': '\u9647\u897f\u53bf', 'level': 3 },
          '3094': { 'id': 3094, 'pid': 3090, 'name': '\u6e2d\u6e90\u53bf', 'level': 3 },
          '3095': { 'id': 3095, 'pid': 3090, 'name': '\u4e34\u6d2e\u53bf', 'level': 3 },
          '3096': { 'id': 3096, 'pid': 3090, 'name': '\u6f33\u53bf', 'level': 3 },
          '3097': { 'id': 3097, 'pid': 3090, 'name': '\u5cb7\u53bf', 'level': 3 }
        }
      },
      '3098': {
        'id': 3098,
        'pid': 3022,
        'name': '\u9647\u5357\u5e02',
        'level': 2,
        'region': {
          '3099': { 'id': 3099, 'pid': 3098, 'name': '\u6b66\u90fd\u533a', 'level': 3 },
          '3100': { 'id': 3100, 'pid': 3098, 'name': '\u6210\u53bf', 'level': 3 },
          '3101': { 'id': 3101, 'pid': 3098, 'name': '\u6587\u53bf', 'level': 3 },
          '3102': { 'id': 3102, 'pid': 3098, 'name': '\u5b95\u660c\u53bf', 'level': 3 },
          '3103': { 'id': 3103, 'pid': 3098, 'name': '\u5eb7\u53bf', 'level': 3 },
          '3104': { 'id': 3104, 'pid': 3098, 'name': '\u897f\u548c\u53bf', 'level': 3 },
          '3105': { 'id': 3105, 'pid': 3098, 'name': '\u793c\u53bf', 'level': 3 },
          '3106': { 'id': 3106, 'pid': 3098, 'name': '\u5fbd\u53bf', 'level': 3 },
          '3107': { 'id': 3107, 'pid': 3098, 'name': '\u4e24\u5f53\u53bf', 'level': 3 }
        }
      },
      '3108': {
        'id': 3108,
        'pid': 3022,
        'name': '\u4e34\u590f\u56de\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3109': { 'id': 3109, 'pid': 3108, 'name': '\u4e34\u590f\u5e02', 'level': 3 },
          '3110': { 'id': 3110, 'pid': 3108, 'name': '\u4e34\u590f\u53bf', 'level': 3 },
          '3111': { 'id': 3111, 'pid': 3108, 'name': '\u5eb7\u4e50\u53bf', 'level': 3 },
          '3112': { 'id': 3112, 'pid': 3108, 'name': '\u6c38\u9756\u53bf', 'level': 3 },
          '3113': { 'id': 3113, 'pid': 3108, 'name': '\u5e7f\u6cb3\u53bf', 'level': 3 },
          '3114': { 'id': 3114, 'pid': 3108, 'name': '\u548c\u653f\u53bf', 'level': 3 },
          '3115': { 'id': 3115, 'pid': 3108, 'name': '\u4e1c\u4e61\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '3116': {
            'id': 3116,
            'pid': 3108,
            'name': '\u79ef\u77f3\u5c71\u4fdd\u5b89\u65cf\u4e1c\u4e61\u65cf\u6492\u62c9\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          }
        }
      },
      '3117': {
        'id': 3117,
        'pid': 3022,
        'name': '\u7518\u5357\u85cf\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3118': { 'id': 3118, 'pid': 3117, 'name': '\u5408\u4f5c\u5e02', 'level': 3 },
          '3119': { 'id': 3119, 'pid': 3117, 'name': '\u4e34\u6f6d\u53bf', 'level': 3 },
          '3120': { 'id': 3120, 'pid': 3117, 'name': '\u5353\u5c3c\u53bf', 'level': 3 },
          '3121': { 'id': 3121, 'pid': 3117, 'name': '\u821f\u66f2\u53bf', 'level': 3 },
          '3122': { 'id': 3122, 'pid': 3117, 'name': '\u8fed\u90e8\u53bf', 'level': 3 },
          '3123': { 'id': 3123, 'pid': 3117, 'name': '\u739b\u66f2\u53bf', 'level': 3 },
          '3124': { 'id': 3124, 'pid': 3117, 'name': '\u788c\u66f2\u53bf', 'level': 3 },
          '3125': { 'id': 3125, 'pid': 3117, 'name': '\u590f\u6cb3\u53bf', 'level': 3 }
        }
      }
    }
  },
  '3126': {
    'id': 3126, 'pid': 0, 'name': '\u9752\u6d77\u7701', 'level': 1, 'city': {
      '3127': {
        'id': 3127,
        'pid': 3126,
        'name': '\u897f\u5b81\u5e02',
        'level': 2,
        'region': {
          '3128': { 'id': 3128, 'pid': 3127, 'name': '\u57ce\u4e1c\u533a', 'level': 3 },
          '3129': { 'id': 3129, 'pid': 3127, 'name': '\u57ce\u4e2d\u533a', 'level': 3 },
          '3130': { 'id': 3130, 'pid': 3127, 'name': '\u57ce\u897f\u533a', 'level': 3 },
          '3131': { 'id': 3131, 'pid': 3127, 'name': '\u57ce\u5317\u533a', 'level': 3 },
          '3132': {
            'id': 3132,
            'pid': 3127,
            'name': '\u5927\u901a\u56de\u65cf\u571f\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '3133': { 'id': 3133, 'pid': 3127, 'name': '\u6e5f\u4e2d\u53bf', 'level': 3 },
          '3134': { 'id': 3134, 'pid': 3127, 'name': '\u6e5f\u6e90\u53bf', 'level': 3 }
        }
      },
      '3135': {
        'id': 3135,
        'pid': 3126,
        'name': '\u6d77\u4e1c\u5e02',
        'level': 2,
        'region': {
          '3136': { 'id': 3136, 'pid': 3135, 'name': '\u4e50\u90fd\u533a', 'level': 3 },
          '3137': { 'id': 3137, 'pid': 3135, 'name': '\u5e73\u5b89\u53bf', 'level': 3 },
          '3138': {
            'id': 3138,
            'pid': 3135,
            'name': '\u6c11\u548c\u56de\u65cf\u571f\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '3139': { 'id': 3139, 'pid': 3135, 'name': '\u4e92\u52a9\u571f\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '3140': { 'id': 3140, 'pid': 3135, 'name': '\u5316\u9686\u56de\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '3141': { 'id': 3141, 'pid': 3135, 'name': '\u5faa\u5316\u6492\u62c9\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '3142': {
        'id': 3142,
        'pid': 3126,
        'name': '\u6d77\u5317\u85cf\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3143': {
            'id': 3143,
            'pid': 3142,
            'name': '\u95e8\u6e90\u56de\u65cf\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '3144': { 'id': 3144, 'pid': 3142, 'name': '\u7941\u8fde\u53bf', 'level': 3 },
          '3145': { 'id': 3145, 'pid': 3142, 'name': '\u6d77\u664f\u53bf', 'level': 3 },
          '3146': { 'id': 3146, 'pid': 3142, 'name': '\u521a\u5bdf\u53bf', 'level': 3 }
        }
      },
      '3147': {
        'id': 3147,
        'pid': 3126,
        'name': '\u9ec4\u5357\u85cf\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3148': { 'id': 3148, 'pid': 3147, 'name': '\u540c\u4ec1\u53bf', 'level': 3 },
          '3149': { 'id': 3149, 'pid': 3147, 'name': '\u5c16\u624e\u53bf', 'level': 3 },
          '3150': { 'id': 3150, 'pid': 3147, 'name': '\u6cfd\u5e93\u53bf', 'level': 3 },
          '3151': { 'id': 3151, 'pid': 3147, 'name': '\u6cb3\u5357\u8499\u53e4\u65cf\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '3152': {
        'id': 3152,
        'pid': 3126,
        'name': '\u6d77\u5357\u85cf\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3153': { 'id': 3153, 'pid': 3152, 'name': '\u5171\u548c\u53bf', 'level': 3 },
          '3154': { 'id': 3154, 'pid': 3152, 'name': '\u540c\u5fb7\u53bf', 'level': 3 },
          '3155': { 'id': 3155, 'pid': 3152, 'name': '\u8d35\u5fb7\u53bf', 'level': 3 },
          '3156': { 'id': 3156, 'pid': 3152, 'name': '\u5174\u6d77\u53bf', 'level': 3 },
          '3157': { 'id': 3157, 'pid': 3152, 'name': '\u8d35\u5357\u53bf', 'level': 3 }
        }
      },
      '3158': {
        'id': 3158,
        'pid': 3126,
        'name': '\u679c\u6d1b\u85cf\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3159': { 'id': 3159, 'pid': 3158, 'name': '\u739b\u6c81\u53bf', 'level': 3 },
          '3160': { 'id': 3160, 'pid': 3158, 'name': '\u73ed\u739b\u53bf', 'level': 3 },
          '3161': { 'id': 3161, 'pid': 3158, 'name': '\u7518\u5fb7\u53bf', 'level': 3 },
          '3162': { 'id': 3162, 'pid': 3158, 'name': '\u8fbe\u65e5\u53bf', 'level': 3 },
          '3163': { 'id': 3163, 'pid': 3158, 'name': '\u4e45\u6cbb\u53bf', 'level': 3 },
          '3164': { 'id': 3164, 'pid': 3158, 'name': '\u739b\u591a\u53bf', 'level': 3 }
        }
      },
      '3165': {
        'id': 3165,
        'pid': 3126,
        'name': '\u7389\u6811\u85cf\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3166': { 'id': 3166, 'pid': 3165, 'name': '\u7389\u6811\u5e02', 'level': 3 },
          '3167': { 'id': 3167, 'pid': 3165, 'name': '\u6742\u591a\u53bf', 'level': 3 },
          '3168': { 'id': 3168, 'pid': 3165, 'name': '\u79f0\u591a\u53bf', 'level': 3 },
          '3169': { 'id': 3169, 'pid': 3165, 'name': '\u6cbb\u591a\u53bf', 'level': 3 },
          '3170': { 'id': 3170, 'pid': 3165, 'name': '\u56ca\u8c26\u53bf', 'level': 3 },
          '3171': { 'id': 3171, 'pid': 3165, 'name': '\u66f2\u9ebb\u83b1\u53bf', 'level': 3 }
        }
      },
      '3172': {
        'id': 3172,
        'pid': 3126,
        'name': '\u6d77\u897f\u8499\u53e4\u65cf\u85cf\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3173': { 'id': 3173, 'pid': 3172, 'name': '\u683c\u5c14\u6728\u5e02', 'level': 3 },
          '3174': { 'id': 3174, 'pid': 3172, 'name': '\u5fb7\u4ee4\u54c8\u5e02', 'level': 3 },
          '3175': { 'id': 3175, 'pid': 3172, 'name': '\u4e4c\u5170\u53bf', 'level': 3 },
          '3176': { 'id': 3176, 'pid': 3172, 'name': '\u90fd\u5170\u53bf', 'level': 3 },
          '3177': { 'id': 3177, 'pid': 3172, 'name': '\u5929\u5cfb\u53bf', 'level': 3 }
        }
      }
    }
  },
  '3178': {
    'id': 3178, 'pid': 0, 'name': '\u5b81\u590f\u56de\u65cf\u81ea\u6cbb\u533a', 'level': 1, 'city': {
      '3179': {
        'id': 3179,
        'pid': 3178,
        'name': '\u94f6\u5ddd\u5e02',
        'level': 2,
        'region': {
          '3180': { 'id': 3180, 'pid': 3179, 'name': '\u5174\u5e86\u533a', 'level': 3 },
          '3181': { 'id': 3181, 'pid': 3179, 'name': '\u897f\u590f\u533a', 'level': 3 },
          '3182': { 'id': 3182, 'pid': 3179, 'name': '\u91d1\u51e4\u533a', 'level': 3 },
          '3183': { 'id': 3183, 'pid': 3179, 'name': '\u6c38\u5b81\u53bf', 'level': 3 },
          '3184': { 'id': 3184, 'pid': 3179, 'name': '\u8d3a\u5170\u53bf', 'level': 3 },
          '3185': { 'id': 3185, 'pid': 3179, 'name': '\u7075\u6b66\u5e02', 'level': 3 }
        }
      },
      '3186': {
        'id': 3186,
        'pid': 3178,
        'name': '\u77f3\u5634\u5c71\u5e02',
        'level': 2,
        'region': {
          '3187': { 'id': 3187, 'pid': 3186, 'name': '\u5927\u6b66\u53e3\u533a', 'level': 3 },
          '3188': { 'id': 3188, 'pid': 3186, 'name': '\u60e0\u519c\u533a', 'level': 3 },
          '3189': { 'id': 3189, 'pid': 3186, 'name': '\u5e73\u7f57\u53bf', 'level': 3 }
        }
      },
      '3190': {
        'id': 3190,
        'pid': 3178,
        'name': '\u5434\u5fe0\u5e02',
        'level': 2,
        'region': {
          '3191': { 'id': 3191, 'pid': 3190, 'name': '\u5229\u901a\u533a', 'level': 3 },
          '3192': { 'id': 3192, 'pid': 3190, 'name': '\u7ea2\u5bfa\u5821\u533a', 'level': 3 },
          '3193': { 'id': 3193, 'pid': 3190, 'name': '\u76d0\u6c60\u53bf', 'level': 3 },
          '3194': { 'id': 3194, 'pid': 3190, 'name': '\u540c\u5fc3\u53bf', 'level': 3 },
          '3195': { 'id': 3195, 'pid': 3190, 'name': '\u9752\u94dc\u5ce1\u5e02', 'level': 3 }
        }
      },
      '3196': {
        'id': 3196,
        'pid': 3178,
        'name': '\u56fa\u539f\u5e02',
        'level': 2,
        'region': {
          '3197': { 'id': 3197, 'pid': 3196, 'name': '\u539f\u5dde\u533a', 'level': 3 },
          '3198': { 'id': 3198, 'pid': 3196, 'name': '\u897f\u5409\u53bf', 'level': 3 },
          '3199': { 'id': 3199, 'pid': 3196, 'name': '\u9686\u5fb7\u53bf', 'level': 3 },
          '3200': { 'id': 3200, 'pid': 3196, 'name': '\u6cfe\u6e90\u53bf', 'level': 3 },
          '3201': { 'id': 3201, 'pid': 3196, 'name': '\u5f6d\u9633\u53bf', 'level': 3 }
        }
      },
      '3202': {
        'id': 3202,
        'pid': 3178,
        'name': '\u4e2d\u536b\u5e02',
        'level': 2,
        'region': {
          '3203': { 'id': 3203, 'pid': 3202, 'name': '\u6c99\u5761\u5934\u533a', 'level': 3 },
          '3204': { 'id': 3204, 'pid': 3202, 'name': '\u4e2d\u5b81\u53bf', 'level': 3 },
          '3205': { 'id': 3205, 'pid': 3202, 'name': '\u6d77\u539f\u53bf', 'level': 3 }
        }
      }
    }
  },
  '3206': {
    'id': 3206, 'pid': 0, 'name': '\u65b0\u7586\u7ef4\u543e\u5c14\u81ea\u6cbb\u533a', 'level': 1, 'city': {
      '3207': {
        'id': 3207,
        'pid': 3206,
        'name': '\u4e4c\u9c81\u6728\u9f50\u5e02',
        'level': 2,
        'region': {
          '3208': { 'id': 3208, 'pid': 3207, 'name': '\u5929\u5c71\u533a', 'level': 3 },
          '3209': { 'id': 3209, 'pid': 3207, 'name': '\u6c99\u4f9d\u5df4\u514b\u533a', 'level': 3 },
          '3210': { 'id': 3210, 'pid': 3207, 'name': '\u65b0\u5e02\u533a', 'level': 3 },
          '3211': { 'id': 3211, 'pid': 3207, 'name': '\u6c34\u78e8\u6c9f\u533a', 'level': 3 },
          '3212': { 'id': 3212, 'pid': 3207, 'name': '\u5934\u5c6f\u6cb3\u533a', 'level': 3 },
          '3213': { 'id': 3213, 'pid': 3207, 'name': '\u8fbe\u5742\u57ce\u533a', 'level': 3 },
          '3214': { 'id': 3214, 'pid': 3207, 'name': '\u7c73\u4e1c\u533a', 'level': 3 },
          '3215': { 'id': 3215, 'pid': 3207, 'name': '\u4e4c\u9c81\u6728\u9f50\u53bf', 'level': 3 }
        }
      },
      '3216': {
        'id': 3216,
        'pid': 3206,
        'name': '\u514b\u62c9\u739b\u4f9d\u5e02',
        'level': 2,
        'region': {
          '3217': { 'id': 3217, 'pid': 3216, 'name': '\u72ec\u5c71\u5b50\u533a', 'level': 3 },
          '3218': { 'id': 3218, 'pid': 3216, 'name': '\u514b\u62c9\u739b\u4f9d\u533a', 'level': 3 },
          '3219': { 'id': 3219, 'pid': 3216, 'name': '\u767d\u78b1\u6ee9\u533a', 'level': 3 },
          '3220': { 'id': 3220, 'pid': 3216, 'name': '\u4e4c\u5c14\u79be\u533a', 'level': 3 }
        }
      },
      '3221': {
        'id': 3221,
        'pid': 3206,
        'name': '\u5410\u9c81\u756a\u5730\u533a',
        'level': 2,
        'region': {
          '3222': { 'id': 3222, 'pid': 3221, 'name': '\u5410\u9c81\u756a\u5e02', 'level': 3 },
          '3223': { 'id': 3223, 'pid': 3221, 'name': '\u912f\u5584\u53bf', 'level': 3 },
          '3224': { 'id': 3224, 'pid': 3221, 'name': '\u6258\u514b\u900a\u53bf', 'level': 3 }
        }
      },
      '3225': {
        'id': 3225,
        'pid': 3206,
        'name': '\u54c8\u5bc6\u5730\u533a',
        'level': 2,
        'region': {
          '3226': { 'id': 3226, 'pid': 3225, 'name': '\u54c8\u5bc6\u5e02', 'level': 3 },
          '3227': {
            'id': 3227,
            'pid': 3225,
            'name': '\u5df4\u91cc\u5764\u54c8\u8428\u514b\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '3228': { 'id': 3228, 'pid': 3225, 'name': '\u4f0a\u543e\u53bf', 'level': 3 }
        }
      },
      '3229': {
        'id': 3229,
        'pid': 3206,
        'name': '\u660c\u5409\u56de\u65cf\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3230': { 'id': 3230, 'pid': 3229, 'name': '\u660c\u5409\u5e02', 'level': 3 },
          '3231': { 'id': 3231, 'pid': 3229, 'name': '\u961c\u5eb7\u5e02', 'level': 3 },
          '3232': { 'id': 3232, 'pid': 3229, 'name': '\u547c\u56fe\u58c1\u53bf', 'level': 3 },
          '3233': { 'id': 3233, 'pid': 3229, 'name': '\u739b\u7eb3\u65af\u53bf', 'level': 3 },
          '3234': { 'id': 3234, 'pid': 3229, 'name': '\u5947\u53f0\u53bf', 'level': 3 },
          '3235': { 'id': 3235, 'pid': 3229, 'name': '\u5409\u6728\u8428\u5c14\u53bf', 'level': 3 },
          '3236': { 'id': 3236, 'pid': 3229, 'name': '\u6728\u5792\u54c8\u8428\u514b\u81ea\u6cbb\u53bf', 'level': 3 }
        }
      },
      '3237': {
        'id': 3237,
        'pid': 3206,
        'name': '\u535a\u5c14\u5854\u62c9\u8499\u53e4\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3238': { 'id': 3238, 'pid': 3237, 'name': '\u535a\u4e50\u5e02', 'level': 3 },
          '3239': { 'id': 3239, 'pid': 3237, 'name': '\u963f\u62c9\u5c71\u53e3\u5e02', 'level': 3 },
          '3240': { 'id': 3240, 'pid': 3237, 'name': '\u7cbe\u6cb3\u53bf', 'level': 3 },
          '3241': { 'id': 3241, 'pid': 3237, 'name': '\u6e29\u6cc9\u53bf', 'level': 3 }
        }
      },
      '3242': {
        'id': 3242,
        'pid': 3206,
        'name': '\u5df4\u97f3\u90ed\u695e\u8499\u53e4\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3243': { 'id': 3243, 'pid': 3242, 'name': '\u5e93\u5c14\u52d2\u5e02', 'level': 3 },
          '3244': { 'id': 3244, 'pid': 3242, 'name': '\u8f6e\u53f0\u53bf', 'level': 3 },
          '3245': { 'id': 3245, 'pid': 3242, 'name': '\u5c09\u7281\u53bf', 'level': 3 },
          '3246': { 'id': 3246, 'pid': 3242, 'name': '\u82e5\u7f8c\u53bf', 'level': 3 },
          '3247': { 'id': 3247, 'pid': 3242, 'name': '\u4e14\u672b\u53bf', 'level': 3 },
          '3248': { 'id': 3248, 'pid': 3242, 'name': '\u7109\u8006\u56de\u65cf\u81ea\u6cbb\u53bf', 'level': 3 },
          '3249': { 'id': 3249, 'pid': 3242, 'name': '\u548c\u9759\u53bf', 'level': 3 },
          '3250': { 'id': 3250, 'pid': 3242, 'name': '\u548c\u7855\u53bf', 'level': 3 },
          '3251': { 'id': 3251, 'pid': 3242, 'name': '\u535a\u6e56\u53bf', 'level': 3 }
        }
      },
      '3252': {
        'id': 3252,
        'pid': 3206,
        'name': '\u963f\u514b\u82cf\u5730\u533a',
        'level': 2,
        'region': {
          '3253': { 'id': 3253, 'pid': 3252, 'name': '\u963f\u514b\u82cf\u5e02', 'level': 3 },
          '3254': { 'id': 3254, 'pid': 3252, 'name': '\u6e29\u5bbf\u53bf', 'level': 3 },
          '3255': { 'id': 3255, 'pid': 3252, 'name': '\u5e93\u8f66\u53bf', 'level': 3 },
          '3256': { 'id': 3256, 'pid': 3252, 'name': '\u6c99\u96c5\u53bf', 'level': 3 },
          '3257': { 'id': 3257, 'pid': 3252, 'name': '\u65b0\u548c\u53bf', 'level': 3 },
          '3258': { 'id': 3258, 'pid': 3252, 'name': '\u62dc\u57ce\u53bf', 'level': 3 },
          '3259': { 'id': 3259, 'pid': 3252, 'name': '\u4e4c\u4ec0\u53bf', 'level': 3 },
          '3260': { 'id': 3260, 'pid': 3252, 'name': '\u963f\u74e6\u63d0\u53bf', 'level': 3 },
          '3261': { 'id': 3261, 'pid': 3252, 'name': '\u67ef\u576a\u53bf', 'level': 3 }
        }
      },
      '3262': {
        'id': 3262,
        'pid': 3206,
        'name': '\u514b\u5b5c\u52d2\u82cf\u67ef\u5c14\u514b\u5b5c\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3263': { 'id': 3263, 'pid': 3262, 'name': '\u963f\u56fe\u4ec0\u5e02', 'level': 3 },
          '3264': { 'id': 3264, 'pid': 3262, 'name': '\u963f\u514b\u9676\u53bf', 'level': 3 },
          '3265': { 'id': 3265, 'pid': 3262, 'name': '\u963f\u5408\u5947\u53bf', 'level': 3 },
          '3266': { 'id': 3266, 'pid': 3262, 'name': '\u4e4c\u6070\u53bf', 'level': 3 }
        }
      },
      '3267': {
        'id': 3267,
        'pid': 3206,
        'name': '\u5580\u4ec0\u5730\u533a',
        'level': 2,
        'region': {
          '3268': { 'id': 3268, 'pid': 3267, 'name': '\u5580\u4ec0\u5e02', 'level': 3 },
          '3269': { 'id': 3269, 'pid': 3267, 'name': '\u758f\u9644\u53bf', 'level': 3 },
          '3270': { 'id': 3270, 'pid': 3267, 'name': '\u758f\u52d2\u53bf', 'level': 3 },
          '3271': { 'id': 3271, 'pid': 3267, 'name': '\u82f1\u5409\u6c99\u53bf', 'level': 3 },
          '3272': { 'id': 3272, 'pid': 3267, 'name': '\u6cfd\u666e\u53bf', 'level': 3 },
          '3273': { 'id': 3273, 'pid': 3267, 'name': '\u838e\u8f66\u53bf', 'level': 3 },
          '3274': { 'id': 3274, 'pid': 3267, 'name': '\u53f6\u57ce\u53bf', 'level': 3 },
          '3275': { 'id': 3275, 'pid': 3267, 'name': '\u9ea6\u76d6\u63d0\u53bf', 'level': 3 },
          '3276': { 'id': 3276, 'pid': 3267, 'name': '\u5cb3\u666e\u6e56\u53bf', 'level': 3 },
          '3277': { 'id': 3277, 'pid': 3267, 'name': '\u4f3d\u5e08\u53bf', 'level': 3 },
          '3278': { 'id': 3278, 'pid': 3267, 'name': '\u5df4\u695a\u53bf', 'level': 3 },
          '3279': {
            'id': 3279,
            'pid': 3267,
            'name': '\u5854\u4ec0\u5e93\u5c14\u5e72\u5854\u5409\u514b\u81ea\u6cbb\u53bf',
            'level': 3
          }
        }
      },
      '3280': {
        'id': 3280,
        'pid': 3206,
        'name': '\u548c\u7530\u5730\u533a',
        'level': 2,
        'region': {
          '3281': { 'id': 3281, 'pid': 3280, 'name': '\u548c\u7530\u5e02', 'level': 3 },
          '3282': { 'id': 3282, 'pid': 3280, 'name': '\u548c\u7530\u53bf', 'level': 3 },
          '3283': { 'id': 3283, 'pid': 3280, 'name': '\u58a8\u7389\u53bf', 'level': 3 },
          '3284': { 'id': 3284, 'pid': 3280, 'name': '\u76ae\u5c71\u53bf', 'level': 3 },
          '3285': { 'id': 3285, 'pid': 3280, 'name': '\u6d1b\u6d66\u53bf', 'level': 3 },
          '3286': { 'id': 3286, 'pid': 3280, 'name': '\u7b56\u52d2\u53bf', 'level': 3 },
          '3287': { 'id': 3287, 'pid': 3280, 'name': '\u4e8e\u7530\u53bf', 'level': 3 },
          '3288': { 'id': 3288, 'pid': 3280, 'name': '\u6c11\u4e30\u53bf', 'level': 3 }
        }
      },
      '3289': {
        'id': 3289,
        'pid': 3206,
        'name': '\u4f0a\u7281\u54c8\u8428\u514b\u81ea\u6cbb\u5dde',
        'level': 2,
        'region': {
          '3290': { 'id': 3290, 'pid': 3289, 'name': '\u4f0a\u5b81\u5e02', 'level': 3 },
          '3291': { 'id': 3291, 'pid': 3289, 'name': '\u594e\u5c6f\u5e02', 'level': 3 },
          '3292': { 'id': 3292, 'pid': 3289, 'name': '\u970d\u5c14\u679c\u65af\u5e02', 'level': 3 },
          '3293': { 'id': 3293, 'pid': 3289, 'name': '\u4f0a\u5b81\u53bf', 'level': 3 },
          '3294': {
            'id': 3294,
            'pid': 3289,
            'name': '\u5bdf\u5e03\u67e5\u5c14\u9521\u4f2f\u81ea\u6cbb\u53bf',
            'level': 3
          },
          '3295': { 'id': 3295, 'pid': 3289, 'name': '\u970d\u57ce\u53bf', 'level': 3 },
          '3296': { 'id': 3296, 'pid': 3289, 'name': '\u5de9\u7559\u53bf', 'level': 3 },
          '3297': { 'id': 3297, 'pid': 3289, 'name': '\u65b0\u6e90\u53bf', 'level': 3 },
          '3298': { 'id': 3298, 'pid': 3289, 'name': '\u662d\u82cf\u53bf', 'level': 3 },
          '3299': { 'id': 3299, 'pid': 3289, 'name': '\u7279\u514b\u65af\u53bf', 'level': 3 },
          '3300': { 'id': 3300, 'pid': 3289, 'name': '\u5c3c\u52d2\u514b\u53bf', 'level': 3 }
        }
      },
      '3301': {
        'id': 3301,
        'pid': 3206,
        'name': '\u5854\u57ce\u5730\u533a',
        'level': 2,
        'region': {
          '3302': { 'id': 3302, 'pid': 3301, 'name': '\u5854\u57ce\u5e02', 'level': 3 },
          '3303': { 'id': 3303, 'pid': 3301, 'name': '\u4e4c\u82cf\u5e02', 'level': 3 },
          '3304': { 'id': 3304, 'pid': 3301, 'name': '\u989d\u654f\u53bf', 'level': 3 },
          '3305': { 'id': 3305, 'pid': 3301, 'name': '\u6c99\u6e7e\u53bf', 'level': 3 },
          '3306': { 'id': 3306, 'pid': 3301, 'name': '\u6258\u91cc\u53bf', 'level': 3 },
          '3307': { 'id': 3307, 'pid': 3301, 'name': '\u88d5\u6c11\u53bf', 'level': 3 },
          '3308': {
            'id': 3308,
            'pid': 3301,
            'name': '\u548c\u5e03\u514b\u8d5b\u5c14\u8499\u53e4\u81ea\u6cbb\u53bf',
            'level': 3
          }
        }
      },
      '3309': {
        'id': 3309,
        'pid': 3206,
        'name': '\u963f\u52d2\u6cf0\u5730\u533a',
        'level': 2,
        'region': {
          '3310': { 'id': 3310, 'pid': 3309, 'name': '\u963f\u52d2\u6cf0\u5e02', 'level': 3 },
          '3311': { 'id': 3311, 'pid': 3309, 'name': '\u5e03\u5c14\u6d25\u53bf', 'level': 3 },
          '3312': { 'id': 3312, 'pid': 3309, 'name': '\u5bcc\u8574\u53bf', 'level': 3 },
          '3313': { 'id': 3313, 'pid': 3309, 'name': '\u798f\u6d77\u53bf', 'level': 3 },
          '3314': { 'id': 3314, 'pid': 3309, 'name': '\u54c8\u5df4\u6cb3\u53bf', 'level': 3 },
          '3315': { 'id': 3315, 'pid': 3309, 'name': '\u9752\u6cb3\u53bf', 'level': 3 },
          '3316': { 'id': 3316, 'pid': 3309, 'name': '\u5409\u6728\u4e43\u53bf', 'level': 3 }
        }
      },
      '3317': {
        'id': 3317,
        'pid': 3206,
        'name': '\u76f4\u8f96\u53bf\u7ea7',
        'level': 2,
        'region': {
          '3318': { 'id': 3318, 'pid': 3317, 'name': '\u77f3\u6cb3\u5b50\u5e02', 'level': 3 },
          '3319': { 'id': 3319, 'pid': 3317, 'name': '\u963f\u62c9\u5c14\u5e02', 'level': 3 },
          '3320': { 'id': 3320, 'pid': 3317, 'name': '\u56fe\u6728\u8212\u514b\u5e02', 'level': 3 },
          '3321': { 'id': 3321, 'pid': 3317, 'name': '\u4e94\u5bb6\u6e20\u5e02', 'level': 3 },
          '3322': { 'id': 3322, 'pid': 3317, 'name': '\u5317\u5c6f\u5e02', 'level': 3 },
          '3323': { 'id': 3323, 'pid': 3317, 'name': '\u94c1\u95e8\u5173\u5e02', 'level': 3 },
          '3324': { 'id': 3324, 'pid': 3317, 'name': '\u53cc\u6cb3\u5e02', 'level': 3 }
        }
      }
    }
  },
  '3325': {
    'id': 3325, 'pid': 0, 'name': '\u53f0\u6e7e\u7701', 'level': 1, 'city': {
      '3326': {
        'id': 3326,
        'pid': 3325,
        'name': '\u53f0\u5317\u5e02',
        'level': 2,
        'region': {
          '3327': { 'id': 3327, 'pid': 3326, 'name': '\u677e\u5c71\u533a', 'level': 3 },
          '3328': { 'id': 3328, 'pid': 3326, 'name': '\u4fe1\u4e49\u533a', 'level': 3 },
          '3329': { 'id': 3329, 'pid': 3326, 'name': '\u5927\u5b89\u533a', 'level': 3 },
          '3330': { 'id': 3330, 'pid': 3326, 'name': '\u4e2d\u5c71\u533a', 'level': 3 },
          '3331': { 'id': 3331, 'pid': 3326, 'name': '\u4e2d\u6b63\u533a', 'level': 3 },
          '3332': { 'id': 3332, 'pid': 3326, 'name': '\u5927\u540c\u533a', 'level': 3 },
          '3333': { 'id': 3333, 'pid': 3326, 'name': '\u4e07\u534e\u533a', 'level': 3 },
          '3334': { 'id': 3334, 'pid': 3326, 'name': '\u6587\u5c71\u533a', 'level': 3 },
          '3335': { 'id': 3335, 'pid': 3326, 'name': '\u5357\u6e2f\u533a', 'level': 3 },
          '3336': { 'id': 3336, 'pid': 3326, 'name': '\u5185\u6e56\u533a', 'level': 3 },
          '3337': { 'id': 3337, 'pid': 3326, 'name': '\u58eb\u6797\u533a', 'level': 3 },
          '3338': { 'id': 3338, 'pid': 3326, 'name': '\u5317\u6295\u533a', 'level': 3 }
        }
      },
      '3339': {
        'id': 3339, 'pid': 3325, 'name': '\u9ad8\u96c4\u5e02', 'level': 2, 'region': {
          '3340': { 'id': 3340, 'pid': 3339, 'name': '\u76d0\u57d5\u533a', 'level': 3 },
          '3341': { 'id': 3341, 'pid': 3339, 'name': '\u9f13\u5c71\u533a', 'level': 3 },
          '3342': { 'id': 3342, 'pid': 3339, 'name': '\u5de6\u8425\u533a', 'level': 3 },
          '3343': { 'id': 3343, 'pid': 3339, 'name': '\u6960\u6893\u533a', 'level': 3 },
          '3344': { 'id': 3344, 'pid': 3339, 'name': '\u4e09\u6c11\u533a', 'level': 3 },
          '3345': { 'id': 3345, 'pid': 3339, 'name': '\u65b0\u5174\u533a', 'level': 3 },
          '3346': { 'id': 3346, 'pid': 3339, 'name': '\u524d\u91d1\u533a', 'level': 3 },
          '3347': { 'id': 3347, 'pid': 3339, 'name': '\u82d3\u96c5\u533a', 'level': 3 },
          '3348': { 'id': 3348, 'pid': 3339, 'name': '\u524d\u9547\u533a', 'level': 3 },
          '3349': { 'id': 3349, 'pid': 3339, 'name': '\u65d7\u6d25\u533a', 'level': 3 },
          '3350': { 'id': 3350, 'pid': 3339, 'name': '\u5c0f\u6e2f\u533a', 'level': 3 },
          '3351': { 'id': 3351, 'pid': 3339, 'name': '\u51e4\u5c71\u533a', 'level': 3 },
          '3352': { 'id': 3352, 'pid': 3339, 'name': '\u6797\u56ed\u533a', 'level': 3 },
          '3353': { 'id': 3353, 'pid': 3339, 'name': '\u5927\u5bee\u533a', 'level': 3 },
          '3354': { 'id': 3354, 'pid': 3339, 'name': '\u5927\u6811\u533a', 'level': 3 },
          '3355': { 'id': 3355, 'pid': 3339, 'name': '\u5927\u793e\u533a', 'level': 3 },
          '3356': { 'id': 3356, 'pid': 3339, 'name': '\u4ec1\u6b66\u533a', 'level': 3 },
          '3357': { 'id': 3357, 'pid': 3339, 'name': '\u9e1f\u677e\u533a', 'level': 3 },
          '3358': { 'id': 3358, 'pid': 3339, 'name': '\u5188\u5c71\u533a', 'level': 3 },
          '3359': { 'id': 3359, 'pid': 3339, 'name': '\u6865\u5934\u533a', 'level': 3 },
          '3360': { 'id': 3360, 'pid': 3339, 'name': '\u71d5\u5de2\u533a', 'level': 3 },
          '3361': { 'id': 3361, 'pid': 3339, 'name': '\u7530\u5bee\u533a', 'level': 3 },
          '3362': { 'id': 3362, 'pid': 3339, 'name': '\u963f\u83b2\u533a', 'level': 3 },
          '3363': { 'id': 3363, 'pid': 3339, 'name': '\u8def\u7af9\u533a', 'level': 3 },
          '3364': { 'id': 3364, 'pid': 3339, 'name': '\u6e56\u5185\u533a', 'level': 3 },
          '3365': { 'id': 3365, 'pid': 3339, 'name': '\u8304\u8423\u533a', 'level': 3 },
          '3366': { 'id': 3366, 'pid': 3339, 'name': '\u6c38\u5b89\u533a', 'level': 3 },
          '3367': { 'id': 3367, 'pid': 3339, 'name': '\u5f25\u9640\u533a', 'level': 3 },
          '3368': { 'id': 3368, 'pid': 3339, 'name': '\u6893\u5b98\u533a', 'level': 3 },
          '3369': { 'id': 3369, 'pid': 3339, 'name': '\u65d7\u5c71\u533a', 'level': 3 },
          '3370': { 'id': 3370, 'pid': 3339, 'name': '\u7f8e\u6d53\u533a', 'level': 3 },
          '3371': { 'id': 3371, 'pid': 3339, 'name': '\u516d\u9f9f\u533a', 'level': 3 },
          '3372': { 'id': 3372, 'pid': 3339, 'name': '\u7532\u4ed9\u533a', 'level': 3 },
          '3373': { 'id': 3373, 'pid': 3339, 'name': '\u6749\u6797\u533a', 'level': 3 },
          '3374': { 'id': 3374, 'pid': 3339, 'name': '\u5185\u95e8\u533a', 'level': 3 },
          '3375': { 'id': 3375, 'pid': 3339, 'name': '\u8302\u6797\u533a', 'level': 3 },
          '3376': { 'id': 3376, 'pid': 3339, 'name': '\u6843\u6e90\u533a', 'level': 3 },
          '3377': { 'id': 3377, 'pid': 3339, 'name': '\u90a3\u739b\u590f\u533a', 'level': 3 }
        }
      },
      '3378': {
        'id': 3378,
        'pid': 3325,
        'name': '\u57fa\u9686\u5e02',
        'level': 2,
        'region': {
          '3379': { 'id': 3379, 'pid': 3378, 'name': '\u4e2d\u6b63\u533a', 'level': 3 },
          '3380': { 'id': 3380, 'pid': 3378, 'name': '\u4e03\u5835\u533a', 'level': 3 },
          '3381': { 'id': 3381, 'pid': 3378, 'name': '\u6696\u6696\u533a', 'level': 3 },
          '3382': { 'id': 3382, 'pid': 3378, 'name': '\u4ec1\u7231\u533a', 'level': 3 },
          '3383': { 'id': 3383, 'pid': 3378, 'name': '\u4e2d\u5c71\u533a', 'level': 3 },
          '3384': { 'id': 3384, 'pid': 3378, 'name': '\u5b89\u4e50\u533a', 'level': 3 },
          '3385': { 'id': 3385, 'pid': 3378, 'name': '\u4fe1\u4e49\u533a', 'level': 3 }
        }
      },
      '3386': {
        'id': 3386, 'pid': 3325, 'name': '\u53f0\u4e2d\u5e02', 'level': 2, 'region': {
          '3387': { 'id': 3387, 'pid': 3386, 'name': '\u4e2d\u533a', 'level': 3 },
          '3388': { 'id': 3388, 'pid': 3386, 'name': '\u4e1c\u533a', 'level': 3 },
          '3389': { 'id': 3389, 'pid': 3386, 'name': '\u5357\u533a', 'level': 3 },
          '3390': { 'id': 3390, 'pid': 3386, 'name': '\u897f\u533a', 'level': 3 },
          '3391': { 'id': 3391, 'pid': 3386, 'name': '\u5317\u533a', 'level': 3 },
          '3392': { 'id': 3392, 'pid': 3386, 'name': '\u897f\u5c6f\u533a', 'level': 3 },
          '3393': { 'id': 3393, 'pid': 3386, 'name': '\u5357\u5c6f\u533a', 'level': 3 },
          '3394': { 'id': 3394, 'pid': 3386, 'name': '\u5317\u5c6f\u533a', 'level': 3 },
          '3395': { 'id': 3395, 'pid': 3386, 'name': '\u4e30\u539f\u533a', 'level': 3 },
          '3396': { 'id': 3396, 'pid': 3386, 'name': '\u4e1c\u52bf\u533a', 'level': 3 },
          '3397': { 'id': 3397, 'pid': 3386, 'name': '\u5927\u7532\u533a', 'level': 3 },
          '3398': { 'id': 3398, 'pid': 3386, 'name': '\u6e05\u6c34\u533a', 'level': 3 },
          '3399': { 'id': 3399, 'pid': 3386, 'name': '\u6c99\u9e7f\u533a', 'level': 3 },
          '3400': { 'id': 3400, 'pid': 3386, 'name': '\u68a7\u6816\u533a', 'level': 3 },
          '3401': { 'id': 3401, 'pid': 3386, 'name': '\u540e\u91cc\u533a', 'level': 3 },
          '3402': { 'id': 3402, 'pid': 3386, 'name': '\u795e\u5188\u533a', 'level': 3 },
          '3403': { 'id': 3403, 'pid': 3386, 'name': '\u6f6d\u5b50\u533a', 'level': 3 },
          '3404': { 'id': 3404, 'pid': 3386, 'name': '\u5927\u96c5\u533a', 'level': 3 },
          '3405': { 'id': 3405, 'pid': 3386, 'name': '\u65b0\u793e\u533a', 'level': 3 },
          '3406': { 'id': 3406, 'pid': 3386, 'name': '\u77f3\u5188\u533a', 'level': 3 },
          '3407': { 'id': 3407, 'pid': 3386, 'name': '\u5916\u57d4\u533a', 'level': 3 },
          '3408': { 'id': 3408, 'pid': 3386, 'name': '\u5927\u5b89\u533a', 'level': 3 },
          '3409': { 'id': 3409, 'pid': 3386, 'name': '\u4e4c\u65e5\u533a', 'level': 3 },
          '3410': { 'id': 3410, 'pid': 3386, 'name': '\u5927\u809a\u533a', 'level': 3 },
          '3411': { 'id': 3411, 'pid': 3386, 'name': '\u9f99\u4e95\u533a', 'level': 3 },
          '3412': { 'id': 3412, 'pid': 3386, 'name': '\u96fe\u5cf0\u533a', 'level': 3 },
          '3413': { 'id': 3413, 'pid': 3386, 'name': '\u592a\u5e73\u533a', 'level': 3 },
          '3414': { 'id': 3414, 'pid': 3386, 'name': '\u5927\u91cc\u533a', 'level': 3 },
          '3415': { 'id': 3415, 'pid': 3386, 'name': '\u548c\u5e73\u533a', 'level': 3 }
        }
      },
      '3416': {
        'id': 3416, 'pid': 3325, 'name': '\u53f0\u5357\u5e02', 'level': 2, 'region': {
          '3417': { 'id': 3417, 'pid': 3416, 'name': '\u4e1c\u533a', 'level': 3 },
          '3418': { 'id': 3418, 'pid': 3416, 'name': '\u5357\u533a', 'level': 3 },
          '3419': { 'id': 3419, 'pid': 3416, 'name': '\u5317\u533a', 'level': 3 },
          '3420': { 'id': 3420, 'pid': 3416, 'name': '\u5b89\u5357\u533a', 'level': 3 },
          '3421': { 'id': 3421, 'pid': 3416, 'name': '\u5b89\u5e73\u533a', 'level': 3 },
          '3422': { 'id': 3422, 'pid': 3416, 'name': '\u4e2d\u897f\u533a', 'level': 3 },
          '3423': { 'id': 3423, 'pid': 3416, 'name': '\u65b0\u8425\u533a', 'level': 3 },
          '3424': { 'id': 3424, 'pid': 3416, 'name': '\u76d0\u6c34\u533a', 'level': 3 },
          '3425': { 'id': 3425, 'pid': 3416, 'name': '\u767d\u6cb3\u533a', 'level': 3 },
          '3426': { 'id': 3426, 'pid': 3416, 'name': '\u67f3\u8425\u533a', 'level': 3 },
          '3427': { 'id': 3427, 'pid': 3416, 'name': '\u540e\u58c1\u533a', 'level': 3 },
          '3428': { 'id': 3428, 'pid': 3416, 'name': '\u4e1c\u5c71\u533a', 'level': 3 },
          '3429': { 'id': 3429, 'pid': 3416, 'name': '\u9ebb\u8c46\u533a', 'level': 3 },
          '3430': { 'id': 3430, 'pid': 3416, 'name': '\u4e0b\u8425\u533a', 'level': 3 },
          '3431': { 'id': 3431, 'pid': 3416, 'name': '\u516d\u7532\u533a', 'level': 3 },
          '3432': { 'id': 3432, 'pid': 3416, 'name': '\u5b98\u7530\u533a', 'level': 3 },
          '3433': { 'id': 3433, 'pid': 3416, 'name': '\u5927\u5185\u533a', 'level': 3 },
          '3434': { 'id': 3434, 'pid': 3416, 'name': '\u4f73\u91cc\u533a', 'level': 3 },
          '3435': { 'id': 3435, 'pid': 3416, 'name': '\u5b66\u7532\u533a', 'level': 3 },
          '3436': { 'id': 3436, 'pid': 3416, 'name': '\u897f\u6e2f\u533a', 'level': 3 },
          '3437': { 'id': 3437, 'pid': 3416, 'name': '\u4e03\u80a1\u533a', 'level': 3 },
          '3438': { 'id': 3438, 'pid': 3416, 'name': '\u5c06\u519b\u533a', 'level': 3 },
          '3439': { 'id': 3439, 'pid': 3416, 'name': '\u5317\u95e8\u533a', 'level': 3 },
          '3440': { 'id': 3440, 'pid': 3416, 'name': '\u65b0\u5316\u533a', 'level': 3 },
          '3441': { 'id': 3441, 'pid': 3416, 'name': '\u5584\u5316\u533a', 'level': 3 },
          '3442': { 'id': 3442, 'pid': 3416, 'name': '\u65b0\u5e02\u533a', 'level': 3 },
          '3443': { 'id': 3443, 'pid': 3416, 'name': '\u5b89\u5b9a\u533a', 'level': 3 },
          '3444': { 'id': 3444, 'pid': 3416, 'name': '\u5c71\u4e0a\u533a', 'level': 3 },
          '3445': { 'id': 3445, 'pid': 3416, 'name': '\u7389\u4e95\u533a', 'level': 3 },
          '3446': { 'id': 3446, 'pid': 3416, 'name': '\u6960\u897f\u533a', 'level': 3 },
          '3447': { 'id': 3447, 'pid': 3416, 'name': '\u5357\u5316\u533a', 'level': 3 },
          '3448': { 'id': 3448, 'pid': 3416, 'name': '\u5de6\u9547\u533a', 'level': 3 },
          '3449': { 'id': 3449, 'pid': 3416, 'name': '\u4ec1\u5fb7\u533a', 'level': 3 },
          '3450': { 'id': 3450, 'pid': 3416, 'name': '\u5f52\u4ec1\u533a', 'level': 3 },
          '3451': { 'id': 3451, 'pid': 3416, 'name': '\u5173\u5e99\u533a', 'level': 3 },
          '3452': { 'id': 3452, 'pid': 3416, 'name': '\u9f99\u5d0e\u533a', 'level': 3 },
          '3453': { 'id': 3453, 'pid': 3416, 'name': '\u6c38\u5eb7\u533a', 'level': 3 }
        }
      },
      '3454': {
        'id': 3454,
        'pid': 3325,
        'name': '\u65b0\u7af9\u5e02',
        'level': 2,
        'region': {
          '3455': { 'id': 3455, 'pid': 3454, 'name': '\u4e1c\u533a', 'level': 3 },
          '3456': { 'id': 3456, 'pid': 3454, 'name': '\u5317\u533a', 'level': 3 },
          '3457': { 'id': 3457, 'pid': 3454, 'name': '\u9999\u5c71\u533a', 'level': 3 }
        }
      },
      '3458': {
        'id': 3458,
        'pid': 3325,
        'name': '\u5609\u4e49\u5e02',
        'level': 2,
        'region': {
          '3459': { 'id': 3459, 'pid': 3458, 'name': '\u4e1c\u533a', 'level': 3 },
          '3460': { 'id': 3460, 'pid': 3458, 'name': '\u897f\u533a', 'level': 3 }
        }
      },
      '3461': {
        'id': 3461, 'pid': 3325, 'name': '\u65b0\u5317\u5e02', 'level': 2, 'region': {
          '3462': { 'id': 3462, 'pid': 3461, 'name': '\u677f\u6865\u533a', 'level': 3 },
          '3463': { 'id': 3463, 'pid': 3461, 'name': '\u4e09\u91cd\u533a', 'level': 3 },
          '3464': { 'id': 3464, 'pid': 3461, 'name': '\u4e2d\u548c\u533a', 'level': 3 },
          '3465': { 'id': 3465, 'pid': 3461, 'name': '\u6c38\u548c\u533a', 'level': 3 },
          '3466': { 'id': 3466, 'pid': 3461, 'name': '\u65b0\u5e84\u533a', 'level': 3 },
          '3467': { 'id': 3467, 'pid': 3461, 'name': '\u65b0\u5e97\u533a', 'level': 3 },
          '3468': { 'id': 3468, 'pid': 3461, 'name': '\u6811\u6797\u533a', 'level': 3 },
          '3469': { 'id': 3469, 'pid': 3461, 'name': '\u83ba\u6b4c\u533a', 'level': 3 },
          '3470': { 'id': 3470, 'pid': 3461, 'name': '\u4e09\u5ce1\u533a', 'level': 3 },
          '3471': { 'id': 3471, 'pid': 3461, 'name': '\u6de1\u6c34\u533a', 'level': 3 },
          '3472': { 'id': 3472, 'pid': 3461, 'name': '\u6c50\u6b62\u533a', 'level': 3 },
          '3473': { 'id': 3473, 'pid': 3461, 'name': '\u745e\u82b3\u533a', 'level': 3 },
          '3474': { 'id': 3474, 'pid': 3461, 'name': '\u571f\u57ce\u533a', 'level': 3 },
          '3475': { 'id': 3475, 'pid': 3461, 'name': '\u82a6\u6d32\u533a', 'level': 3 },
          '3476': { 'id': 3476, 'pid': 3461, 'name': '\u4e94\u80a1\u533a', 'level': 3 },
          '3477': { 'id': 3477, 'pid': 3461, 'name': '\u6cf0\u5c71\u533a', 'level': 3 },
          '3478': { 'id': 3478, 'pid': 3461, 'name': '\u6797\u53e3\u533a', 'level': 3 },
          '3479': { 'id': 3479, 'pid': 3461, 'name': '\u6df1\u5751\u533a', 'level': 3 },
          '3480': { 'id': 3480, 'pid': 3461, 'name': '\u77f3\u7887\u533a', 'level': 3 },
          '3481': { 'id': 3481, 'pid': 3461, 'name': '\u576a\u6797\u533a', 'level': 3 },
          '3482': { 'id': 3482, 'pid': 3461, 'name': '\u4e09\u829d\u533a', 'level': 3 },
          '3483': { 'id': 3483, 'pid': 3461, 'name': '\u77f3\u95e8\u533a', 'level': 3 },
          '3484': { 'id': 3484, 'pid': 3461, 'name': '\u516b\u91cc\u533a', 'level': 3 },
          '3485': { 'id': 3485, 'pid': 3461, 'name': '\u5e73\u6eaa\u533a', 'level': 3 },
          '3486': { 'id': 3486, 'pid': 3461, 'name': '\u53cc\u6eaa\u533a', 'level': 3 },
          '3487': { 'id': 3487, 'pid': 3461, 'name': '\u8d21\u5bee\u533a', 'level': 3 },
          '3488': { 'id': 3488, 'pid': 3461, 'name': '\u91d1\u5c71\u533a', 'level': 3 },
          '3489': { 'id': 3489, 'pid': 3461, 'name': '\u4e07\u91cc\u533a', 'level': 3 },
          '3490': { 'id': 3490, 'pid': 3461, 'name': '\u4e4c\u6765\u533a', 'level': 3 }
        }
      },
      '3491': {
        'id': 3491,
        'pid': 3325,
        'name': '\u5b9c\u5170\u53bf',
        'level': 2,
        'region': {
          '3492': { 'id': 3492, 'pid': 3491, 'name': '\u5b9c\u5170\u5e02', 'level': 3 },
          '3493': { 'id': 3493, 'pid': 3491, 'name': '\u7f57\u4e1c\u9547', 'level': 3 },
          '3494': { 'id': 3494, 'pid': 3491, 'name': '\u82cf\u6fb3\u9547', 'level': 3 },
          '3495': { 'id': 3495, 'pid': 3491, 'name': '\u5934\u57ce\u9547', 'level': 3 },
          '3496': { 'id': 3496, 'pid': 3491, 'name': '\u7901\u6eaa\u4e61', 'level': 3 },
          '3497': { 'id': 3497, 'pid': 3491, 'name': '\u58ee\u56f4\u4e61', 'level': 3 },
          '3498': { 'id': 3498, 'pid': 3491, 'name': '\u5458\u5c71\u4e61', 'level': 3 },
          '3499': { 'id': 3499, 'pid': 3491, 'name': '\u51ac\u5c71\u4e61', 'level': 3 },
          '3500': { 'id': 3500, 'pid': 3491, 'name': '\u4e94\u7ed3\u4e61', 'level': 3 },
          '3501': { 'id': 3501, 'pid': 3491, 'name': '\u4e09\u661f\u4e61', 'level': 3 },
          '3502': { 'id': 3502, 'pid': 3491, 'name': '\u5927\u540c\u4e61', 'level': 3 },
          '3503': { 'id': 3503, 'pid': 3491, 'name': '\u5357\u6fb3\u4e61', 'level': 3 }
        }
      },
      '3504': {
        'id': 3504,
        'pid': 3325,
        'name': '\u6843\u56ed\u53bf',
        'level': 2,
        'region': {
          '3505': { 'id': 3505, 'pid': 3504, 'name': '\u6843\u56ed\u5e02', 'level': 3 },
          '3506': { 'id': 3506, 'pid': 3504, 'name': '\u4e2d\u575c\u5e02', 'level': 3 },
          '3507': { 'id': 3507, 'pid': 3504, 'name': '\u5e73\u9547\u5e02', 'level': 3 },
          '3508': { 'id': 3508, 'pid': 3504, 'name': '\u516b\u5fb7\u5e02', 'level': 3 },
          '3509': { 'id': 3509, 'pid': 3504, 'name': '\u6768\u6885\u5e02', 'level': 3 },
          '3510': { 'id': 3510, 'pid': 3504, 'name': '\u82a6\u7af9\u5e02', 'level': 3 },
          '3511': { 'id': 3511, 'pid': 3504, 'name': '\u5927\u6eaa\u9547', 'level': 3 },
          '3512': { 'id': 3512, 'pid': 3504, 'name': '\u5927\u56ed\u4e61', 'level': 3 },
          '3513': { 'id': 3513, 'pid': 3504, 'name': '\u9f9f\u5c71\u4e61', 'level': 3 },
          '3514': { 'id': 3514, 'pid': 3504, 'name': '\u9f99\u6f6d\u4e61', 'level': 3 },
          '3515': { 'id': 3515, 'pid': 3504, 'name': '\u65b0\u5c4b\u4e61', 'level': 3 },
          '3516': { 'id': 3516, 'pid': 3504, 'name': '\u89c2\u97f3\u4e61', 'level': 3 },
          '3517': { 'id': 3517, 'pid': 3504, 'name': '\u590d\u5174\u4e61', 'level': 3 }
        }
      },
      '3518': {
        'id': 3518,
        'pid': 3325,
        'name': '\u65b0\u7af9\u53bf',
        'level': 2,
        'region': {
          '3519': { 'id': 3519, 'pid': 3518, 'name': '\u7af9\u5317\u5e02', 'level': 3 },
          '3520': { 'id': 3520, 'pid': 3518, 'name': '\u7af9\u4e1c\u9547', 'level': 3 },
          '3521': { 'id': 3521, 'pid': 3518, 'name': '\u65b0\u57d4\u9547', 'level': 3 },
          '3522': { 'id': 3522, 'pid': 3518, 'name': '\u5173\u897f\u9547', 'level': 3 },
          '3523': { 'id': 3523, 'pid': 3518, 'name': '\u6e56\u53e3\u4e61', 'level': 3 },
          '3524': { 'id': 3524, 'pid': 3518, 'name': '\u65b0\u4e30\u4e61', 'level': 3 },
          '3525': { 'id': 3525, 'pid': 3518, 'name': '\u828e\u6797\u4e61', 'level': 3 },
          '3526': { 'id': 3526, 'pid': 3518, 'name': '\u6a2a\u5c71\u4e61', 'level': 3 },
          '3527': { 'id': 3527, 'pid': 3518, 'name': '\u5317\u57d4\u4e61', 'level': 3 },
          '3528': { 'id': 3528, 'pid': 3518, 'name': '\u5b9d\u5c71\u4e61', 'level': 3 },
          '3529': { 'id': 3529, 'pid': 3518, 'name': '\u5ce8\u7709\u4e61', 'level': 3 },
          '3530': { 'id': 3530, 'pid': 3518, 'name': '\u5c16\u77f3\u4e61', 'level': 3 },
          '3531': { 'id': 3531, 'pid': 3518, 'name': '\u4e94\u5cf0\u4e61', 'level': 3 }
        }
      },
      '3532': {
        'id': 3532, 'pid': 3325, 'name': '\u82d7\u6817\u53bf', 'level': 2, 'region': {
          '3533': { 'id': 3533, 'pid': 3532, 'name': '\u82d7\u6817\u5e02', 'level': 3 },
          '3534': { 'id': 3534, 'pid': 3532, 'name': '\u82d1\u91cc\u9547', 'level': 3 },
          '3535': { 'id': 3535, 'pid': 3532, 'name': '\u901a\u9704\u9547', 'level': 3 },
          '3536': { 'id': 3536, 'pid': 3532, 'name': '\u7af9\u5357\u9547', 'level': 3 },
          '3537': { 'id': 3537, 'pid': 3532, 'name': '\u5934\u4efd\u9547', 'level': 3 },
          '3538': { 'id': 3538, 'pid': 3532, 'name': '\u540e\u9f99\u9547', 'level': 3 },
          '3539': { 'id': 3539, 'pid': 3532, 'name': '\u5353\u5170\u9547', 'level': 3 },
          '3540': { 'id': 3540, 'pid': 3532, 'name': '\u5927\u6e56\u4e61', 'level': 3 },
          '3541': { 'id': 3541, 'pid': 3532, 'name': '\u516c\u9986\u4e61', 'level': 3 },
          '3542': { 'id': 3542, 'pid': 3532, 'name': '\u94dc\u9523\u4e61', 'level': 3 },
          '3543': { 'id': 3543, 'pid': 3532, 'name': '\u5357\u5e84\u4e61', 'level': 3 },
          '3544': { 'id': 3544, 'pid': 3532, 'name': '\u5934\u5c4b\u4e61', 'level': 3 },
          '3545': { 'id': 3545, 'pid': 3532, 'name': '\u4e09\u4e49\u4e61', 'level': 3 },
          '3546': { 'id': 3546, 'pid': 3532, 'name': '\u897f\u6e56\u4e61', 'level': 3 },
          '3547': { 'id': 3547, 'pid': 3532, 'name': '\u9020\u6865\u4e61', 'level': 3 },
          '3548': { 'id': 3548, 'pid': 3532, 'name': '\u4e09\u6e7e\u4e61', 'level': 3 },
          '3549': { 'id': 3549, 'pid': 3532, 'name': '\u72ee\u6f6d\u4e61', 'level': 3 },
          '3550': { 'id': 3550, 'pid': 3532, 'name': '\u6cf0\u5b89\u4e61', 'level': 3 }
        }
      },
      '3551': {
        'id': 3551, 'pid': 3325, 'name': '\u5f70\u5316\u53bf', 'level': 2, 'region': {
          '3552': { 'id': 3552, 'pid': 3551, 'name': '\u5f70\u5316\u5e02', 'level': 3 },
          '3553': { 'id': 3553, 'pid': 3551, 'name': '\u9e7f\u6e2f\u9547', 'level': 3 },
          '3554': { 'id': 3554, 'pid': 3551, 'name': '\u548c\u7f8e\u9547', 'level': 3 },
          '3555': { 'id': 3555, 'pid': 3551, 'name': '\u7ebf\u897f\u4e61', 'level': 3 },
          '3556': { 'id': 3556, 'pid': 3551, 'name': '\u4f38\u6e2f\u4e61', 'level': 3 },
          '3557': { 'id': 3557, 'pid': 3551, 'name': '\u798f\u5174\u4e61', 'level': 3 },
          '3558': { 'id': 3558, 'pid': 3551, 'name': '\u79c0\u6c34\u4e61', 'level': 3 },
          '3559': { 'id': 3559, 'pid': 3551, 'name': '\u82b1\u575b\u4e61', 'level': 3 },
          '3560': { 'id': 3560, 'pid': 3551, 'name': '\u82ac\u56ed\u4e61', 'level': 3 },
          '3561': { 'id': 3561, 'pid': 3551, 'name': '\u5458\u6797\u9547', 'level': 3 },
          '3562': { 'id': 3562, 'pid': 3551, 'name': '\u6eaa\u6e56\u9547', 'level': 3 },
          '3563': { 'id': 3563, 'pid': 3551, 'name': '\u7530\u4e2d\u9547', 'level': 3 },
          '3564': { 'id': 3564, 'pid': 3551, 'name': '\u5927\u6751\u4e61', 'level': 3 },
          '3565': { 'id': 3565, 'pid': 3551, 'name': '\u57d4\u76d0\u4e61', 'level': 3 },
          '3566': { 'id': 3566, 'pid': 3551, 'name': '\u57d4\u5fc3\u4e61', 'level': 3 },
          '3567': { 'id': 3567, 'pid': 3551, 'name': '\u6c38\u9756\u4e61', 'level': 3 },
          '3568': { 'id': 3568, 'pid': 3551, 'name': '\u793e\u5934\u4e61', 'level': 3 },
          '3569': { 'id': 3569, 'pid': 3551, 'name': '\u4e8c\u6c34\u4e61', 'level': 3 },
          '3570': { 'id': 3570, 'pid': 3551, 'name': '\u5317\u6597\u9547', 'level': 3 },
          '3571': { 'id': 3571, 'pid': 3551, 'name': '\u4e8c\u6797\u9547', 'level': 3 },
          '3572': { 'id': 3572, 'pid': 3551, 'name': '\u7530\u5c3e\u4e61', 'level': 3 },
          '3573': { 'id': 3573, 'pid': 3551, 'name': '\u57e4\u5934\u4e61', 'level': 3 },
          '3574': { 'id': 3574, 'pid': 3551, 'name': '\u82b3\u82d1\u4e61', 'level': 3 },
          '3575': { 'id': 3575, 'pid': 3551, 'name': '\u5927\u57ce\u4e61', 'level': 3 },
          '3576': { 'id': 3576, 'pid': 3551, 'name': '\u7af9\u5858\u4e61', 'level': 3 },
          '3577': { 'id': 3577, 'pid': 3551, 'name': '\u6eaa\u5dde\u4e61', 'level': 3 }
        }
      },
      '3578': {
        'id': 3578,
        'pid': 3325,
        'name': '\u5357\u6295\u53bf',
        'level': 2,
        'region': {
          '3579': { 'id': 3579, 'pid': 3578, 'name': '\u5357\u6295\u5e02', 'level': 3 },
          '3580': { 'id': 3580, 'pid': 3578, 'name': '\u57d4\u91cc\u9547', 'level': 3 },
          '3581': { 'id': 3581, 'pid': 3578, 'name': '\u8349\u5c6f\u9547', 'level': 3 },
          '3582': { 'id': 3582, 'pid': 3578, 'name': '\u7af9\u5c71\u9547', 'level': 3 },
          '3583': { 'id': 3583, 'pid': 3578, 'name': '\u96c6\u96c6\u9547', 'level': 3 },
          '3584': { 'id': 3584, 'pid': 3578, 'name': '\u540d\u95f4\u4e61', 'level': 3 },
          '3585': { 'id': 3585, 'pid': 3578, 'name': '\u9e7f\u8c37\u4e61', 'level': 3 },
          '3586': { 'id': 3586, 'pid': 3578, 'name': '\u4e2d\u5bee\u4e61', 'level': 3 },
          '3587': { 'id': 3587, 'pid': 3578, 'name': '\u9c7c\u6c60\u4e61', 'level': 3 },
          '3588': { 'id': 3588, 'pid': 3578, 'name': '\u56fd\u59d3\u4e61', 'level': 3 },
          '3589': { 'id': 3589, 'pid': 3578, 'name': '\u6c34\u91cc\u4e61', 'level': 3 },
          '3590': { 'id': 3590, 'pid': 3578, 'name': '\u4fe1\u4e49\u4e61', 'level': 3 },
          '3591': { 'id': 3591, 'pid': 3578, 'name': '\u4ec1\u7231\u4e61', 'level': 3 }
        }
      },
      '3592': {
        'id': 3592, 'pid': 3325, 'name': '\u4e91\u6797\u53bf', 'level': 2, 'region': {
          '3593': { 'id': 3593, 'pid': 3592, 'name': '\u6597\u516d\u5e02', 'level': 3 },
          '3594': { 'id': 3594, 'pid': 3592, 'name': '\u6597\u5357\u9547', 'level': 3 },
          '3595': { 'id': 3595, 'pid': 3592, 'name': '\u864e\u5c3e\u9547', 'level': 3 },
          '3596': { 'id': 3596, 'pid': 3592, 'name': '\u897f\u87ba\u9547', 'level': 3 },
          '3597': { 'id': 3597, 'pid': 3592, 'name': '\u571f\u5e93\u9547', 'level': 3 },
          '3598': { 'id': 3598, 'pid': 3592, 'name': '\u5317\u6e2f\u9547', 'level': 3 },
          '3599': { 'id': 3599, 'pid': 3592, 'name': '\u53e4\u5751\u4e61', 'level': 3 },
          '3600': { 'id': 3600, 'pid': 3592, 'name': '\u5927\u57e4\u4e61', 'level': 3 },
          '3601': { 'id': 3601, 'pid': 3592, 'name': '\u83bf\u6850\u4e61', 'level': 3 },
          '3602': { 'id': 3602, 'pid': 3592, 'name': '\u6797\u5185\u4e61', 'level': 3 },
          '3603': { 'id': 3603, 'pid': 3592, 'name': '\u4e8c\u4ed1\u4e61', 'level': 3 },
          '3604': { 'id': 3604, 'pid': 3592, 'name': '\u4ed1\u80cc\u4e61', 'level': 3 },
          '3605': { 'id': 3605, 'pid': 3592, 'name': '\u9ea6\u5bee\u4e61', 'level': 3 },
          '3606': { 'id': 3606, 'pid': 3592, 'name': '\u4e1c\u52bf\u4e61', 'level': 3 },
          '3607': { 'id': 3607, 'pid': 3592, 'name': '\u8912\u5fe0\u4e61', 'level': 3 },
          '3608': { 'id': 3608, 'pid': 3592, 'name': '\u53f0\u897f\u4e61', 'level': 3 },
          '3609': { 'id': 3609, 'pid': 3592, 'name': '\u5143\u957f\u4e61', 'level': 3 },
          '3610': { 'id': 3610, 'pid': 3592, 'name': '\u56db\u6e56\u4e61', 'level': 3 },
          '3611': { 'id': 3611, 'pid': 3592, 'name': '\u53e3\u6e56\u4e61', 'level': 3 },
          '3612': { 'id': 3612, 'pid': 3592, 'name': '\u6c34\u6797\u4e61', 'level': 3 }
        }
      },
      '3613': {
        'id': 3613, 'pid': 3325, 'name': '\u5609\u4e49\u53bf', 'level': 2, 'region': {
          '3614': { 'id': 3614, 'pid': 3613, 'name': '\u592a\u4fdd\u5e02', 'level': 3 },
          '3615': { 'id': 3615, 'pid': 3613, 'name': '\u6734\u5b50\u5e02', 'level': 3 },
          '3616': { 'id': 3616, 'pid': 3613, 'name': '\u5e03\u888b\u9547', 'level': 3 },
          '3617': { 'id': 3617, 'pid': 3613, 'name': '\u5927\u6797\u9547', 'level': 3 },
          '3618': { 'id': 3618, 'pid': 3613, 'name': '\u6c11\u96c4\u4e61', 'level': 3 },
          '3619': { 'id': 3619, 'pid': 3613, 'name': '\u6eaa\u53e3\u4e61', 'level': 3 },
          '3620': { 'id': 3620, 'pid': 3613, 'name': '\u65b0\u6e2f\u4e61', 'level': 3 },
          '3621': { 'id': 3621, 'pid': 3613, 'name': '\u516d\u811a\u4e61', 'level': 3 },
          '3622': { 'id': 3622, 'pid': 3613, 'name': '\u4e1c\u77f3\u4e61', 'level': 3 },
          '3623': { 'id': 3623, 'pid': 3613, 'name': '\u4e49\u7af9\u4e61', 'level': 3 },
          '3624': { 'id': 3624, 'pid': 3613, 'name': '\u9e7f\u8349\u4e61', 'level': 3 },
          '3625': { 'id': 3625, 'pid': 3613, 'name': '\u6c34\u4e0a\u4e61', 'level': 3 },
          '3626': { 'id': 3626, 'pid': 3613, 'name': '\u4e2d\u57d4\u4e61', 'level': 3 },
          '3627': { 'id': 3627, 'pid': 3613, 'name': '\u7af9\u5d0e\u4e61', 'level': 3 },
          '3628': { 'id': 3628, 'pid': 3613, 'name': '\u6885\u5c71\u4e61', 'level': 3 },
          '3629': { 'id': 3629, 'pid': 3613, 'name': '\u756a\u8def\u4e61', 'level': 3 },
          '3630': { 'id': 3630, 'pid': 3613, 'name': '\u5927\u57d4\u4e61', 'level': 3 },
          '3631': { 'id': 3631, 'pid': 3613, 'name': '\u963f\u91cc\u5c71\u4e61', 'level': 3 }
        }
      },
      '3632': {
        'id': 3632, 'pid': 3325, 'name': '\u5c4f\u4e1c\u53bf', 'level': 2, 'region': {
          '3633': { 'id': 3633, 'pid': 3632, 'name': '\u5c4f\u4e1c\u5e02', 'level': 3 },
          '3634': { 'id': 3634, 'pid': 3632, 'name': '\u6f6e\u5dde\u9547', 'level': 3 },
          '3635': { 'id': 3635, 'pid': 3632, 'name': '\u4e1c\u6e2f\u9547', 'level': 3 },
          '3636': { 'id': 3636, 'pid': 3632, 'name': '\u6052\u6625\u9547', 'level': 3 },
          '3637': { 'id': 3637, 'pid': 3632, 'name': '\u4e07\u4e39\u4e61', 'level': 3 },
          '3638': { 'id': 3638, 'pid': 3632, 'name': '\u957f\u6cbb\u4e61', 'level': 3 },
          '3639': { 'id': 3639, 'pid': 3632, 'name': '\u9e9f\u6d1b\u4e61', 'level': 3 },
          '3640': { 'id': 3640, 'pid': 3632, 'name': '\u4e5d\u5982\u4e61', 'level': 3 },
          '3641': { 'id': 3641, 'pid': 3632, 'name': '\u91cc\u6e2f\u4e61', 'level': 3 },
          '3642': { 'id': 3642, 'pid': 3632, 'name': '\u76d0\u57d4\u4e61', 'level': 3 },
          '3643': { 'id': 3643, 'pid': 3632, 'name': '\u9ad8\u6811\u4e61', 'level': 3 },
          '3644': { 'id': 3644, 'pid': 3632, 'name': '\u4e07\u5ce6\u4e61', 'level': 3 },
          '3645': { 'id': 3645, 'pid': 3632, 'name': '\u5185\u57d4\u4e61', 'level': 3 },
          '3646': { 'id': 3646, 'pid': 3632, 'name': '\u7af9\u7530\u4e61', 'level': 3 },
          '3647': { 'id': 3647, 'pid': 3632, 'name': '\u65b0\u57e4\u4e61', 'level': 3 },
          '3648': { 'id': 3648, 'pid': 3632, 'name': '\u678b\u5bee\u4e61', 'level': 3 },
          '3649': { 'id': 3649, 'pid': 3632, 'name': '\u65b0\u56ed\u4e61', 'level': 3 },
          '3650': { 'id': 3650, 'pid': 3632, 'name': '\u5d01\u9876\u4e61', 'level': 3 },
          '3651': { 'id': 3651, 'pid': 3632, 'name': '\u6797\u8fb9\u4e61', 'level': 3 },
          '3652': { 'id': 3652, 'pid': 3632, 'name': '\u5357\u5dde\u4e61', 'level': 3 },
          '3653': { 'id': 3653, 'pid': 3632, 'name': '\u4f73\u51ac\u4e61', 'level': 3 },
          '3654': { 'id': 3654, 'pid': 3632, 'name': '\u7409\u7403\u4e61', 'level': 3 },
          '3655': { 'id': 3655, 'pid': 3632, 'name': '\u8f66\u57ce\u4e61', 'level': 3 },
          '3656': { 'id': 3656, 'pid': 3632, 'name': '\u6ee1\u5dde\u4e61', 'level': 3 },
          '3657': { 'id': 3657, 'pid': 3632, 'name': '\u678b\u5c71\u4e61', 'level': 3 },
          '3658': { 'id': 3658, 'pid': 3632, 'name': '\u4e09\u5730\u95e8\u4e61', 'level': 3 },
          '3659': { 'id': 3659, 'pid': 3632, 'name': '\u96fe\u53f0\u4e61', 'level': 3 },
          '3660': { 'id': 3660, 'pid': 3632, 'name': '\u739b\u5bb6\u4e61', 'level': 3 },
          '3661': { 'id': 3661, 'pid': 3632, 'name': '\u6cf0\u6b66\u4e61', 'level': 3 },
          '3662': { 'id': 3662, 'pid': 3632, 'name': '\u6765\u4e49\u4e61', 'level': 3 },
          '3663': { 'id': 3663, 'pid': 3632, 'name': '\u6625\u65e5\u4e61', 'level': 3 },
          '3664': { 'id': 3664, 'pid': 3632, 'name': '\u72ee\u5b50\u4e61', 'level': 3 },
          '3665': { 'id': 3665, 'pid': 3632, 'name': '\u7261\u4e39\u4e61', 'level': 3 }
        }
      },
      '3666': {
        'id': 3666, 'pid': 3325, 'name': '\u53f0\u4e1c\u53bf', 'level': 2, 'region': {
          '3667': { 'id': 3667, 'pid': 3666, 'name': '\u53f0\u4e1c\u5e02', 'level': 3 },
          '3668': { 'id': 3668, 'pid': 3666, 'name': '\u6210\u529f\u9547', 'level': 3 },
          '3669': { 'id': 3669, 'pid': 3666, 'name': '\u5173\u5c71\u9547', 'level': 3 },
          '3670': { 'id': 3670, 'pid': 3666, 'name': '\u5351\u5357\u4e61', 'level': 3 },
          '3671': { 'id': 3671, 'pid': 3666, 'name': '\u9e7f\u91ce\u4e61', 'level': 3 },
          '3672': { 'id': 3672, 'pid': 3666, 'name': '\u6c60\u4e0a\u4e61', 'level': 3 },
          '3673': { 'id': 3673, 'pid': 3666, 'name': '\u4e1c\u6cb3\u4e61', 'level': 3 },
          '3674': { 'id': 3674, 'pid': 3666, 'name': '\u957f\u6ee8\u4e61', 'level': 3 },
          '3675': { 'id': 3675, 'pid': 3666, 'name': '\u592a\u9ebb\u91cc\u4e61', 'level': 3 },
          '3676': { 'id': 3676, 'pid': 3666, 'name': '\u5927\u6b66\u4e61', 'level': 3 },
          '3677': { 'id': 3677, 'pid': 3666, 'name': '\u7eff\u5c9b\u4e61', 'level': 3 },
          '3678': { 'id': 3678, 'pid': 3666, 'name': '\u6d77\u7aef\u4e61', 'level': 3 },
          '3679': { 'id': 3679, 'pid': 3666, 'name': '\u5ef6\u5e73\u4e61', 'level': 3 },
          '3680': { 'id': 3680, 'pid': 3666, 'name': '\u91d1\u5cf0\u4e61', 'level': 3 },
          '3681': { 'id': 3681, 'pid': 3666, 'name': '\u8fbe\u4ec1\u4e61', 'level': 3 },
          '3682': { 'id': 3682, 'pid': 3666, 'name': '\u5170\u5c7f\u4e61', 'level': 3 }
        }
      },
      '3683': {
        'id': 3683,
        'pid': 3325,
        'name': '\u82b1\u83b2\u53bf',
        'level': 2,
        'region': {
          '3684': { 'id': 3684, 'pid': 3683, 'name': '\u82b1\u83b2\u5e02', 'level': 3 },
          '3685': { 'id': 3685, 'pid': 3683, 'name': '\u51e4\u6797\u9547', 'level': 3 },
          '3686': { 'id': 3686, 'pid': 3683, 'name': '\u7389\u91cc\u9547', 'level': 3 },
          '3687': { 'id': 3687, 'pid': 3683, 'name': '\u65b0\u57ce\u4e61', 'level': 3 },
          '3688': { 'id': 3688, 'pid': 3683, 'name': '\u5409\u5b89\u4e61', 'level': 3 },
          '3689': { 'id': 3689, 'pid': 3683, 'name': '\u5bff\u4e30\u4e61', 'level': 3 },
          '3690': { 'id': 3690, 'pid': 3683, 'name': '\u5149\u590d\u4e61', 'level': 3 },
          '3691': { 'id': 3691, 'pid': 3683, 'name': '\u4e30\u6ee8\u4e61', 'level': 3 },
          '3692': { 'id': 3692, 'pid': 3683, 'name': '\u745e\u7a57\u4e61', 'level': 3 },
          '3693': { 'id': 3693, 'pid': 3683, 'name': '\u5bcc\u91cc\u4e61', 'level': 3 },
          '3694': { 'id': 3694, 'pid': 3683, 'name': '\u79c0\u6797\u4e61', 'level': 3 },
          '3695': { 'id': 3695, 'pid': 3683, 'name': '\u4e07\u8363\u4e61', 'level': 3 },
          '3696': { 'id': 3696, 'pid': 3683, 'name': '\u5353\u6eaa\u4e61', 'level': 3 }
        }
      },
      '3697': {
        'id': 3697,
        'pid': 3325,
        'name': '\u6f8e\u6e56\u53bf',
        'level': 2,
        'region': {
          '3698': { 'id': 3698, 'pid': 3697, 'name': '\u9a6c\u516c\u5e02', 'level': 3 },
          '3699': { 'id': 3699, 'pid': 3697, 'name': '\u6e56\u897f\u4e61', 'level': 3 },
          '3700': { 'id': 3700, 'pid': 3697, 'name': '\u767d\u6c99\u4e61', 'level': 3 },
          '3701': { 'id': 3701, 'pid': 3697, 'name': '\u897f\u5c7f\u4e61', 'level': 3 },
          '3702': { 'id': 3702, 'pid': 3697, 'name': '\u671b\u5b89\u4e61', 'level': 3 },
          '3703': { 'id': 3703, 'pid': 3697, 'name': '\u4e03\u7f8e\u4e61', 'level': 3 }
        }
      },
      '3704': {
        'id': 3704,
        'pid': 3325,
        'name': '\u91d1\u95e8\u53bf',
        'level': 2,
        'region': {
          '3705': { 'id': 3705, 'pid': 3704, 'name': '\u91d1\u57ce\u9547', 'level': 3 },
          '3706': { 'id': 3706, 'pid': 3704, 'name': '\u91d1\u6e56\u9547', 'level': 3 },
          '3707': { 'id': 3707, 'pid': 3704, 'name': '\u91d1\u6c99\u9547', 'level': 3 },
          '3708': { 'id': 3708, 'pid': 3704, 'name': '\u91d1\u5b81\u4e61', 'level': 3 },
          '3709': { 'id': 3709, 'pid': 3704, 'name': '\u70c8\u5c7f\u4e61', 'level': 3 },
          '3710': { 'id': 3710, 'pid': 3704, 'name': '\u4e4c\u4e18\u4e61', 'level': 3 }
        }
      },
      '3711': {
        'id': 3711,
        'pid': 3325,
        'name': '\u8fde\u6c5f\u53bf',
        'level': 2,
        'region': {
          '3712': { 'id': 3712, 'pid': 3711, 'name': '\u5357\u7aff\u4e61', 'level': 3 },
          '3713': { 'id': 3713, 'pid': 3711, 'name': '\u5317\u7aff\u4e61', 'level': 3 },
          '3714': { 'id': 3714, 'pid': 3711, 'name': '\u8392\u5149\u4e61', 'level': 3 },
          '3715': { 'id': 3715, 'pid': 3711, 'name': '\u4e1c\u5f15\u4e61', 'level': 3 }
        }
      }
    }
  },
  '3716': {
    'id': 3716, 'pid': 0, 'name': '\u9999\u6e2f\u7279\u522b\u884c\u653f\u533a', 'level': 1, 'city': {
      '3999': {
        'id': 3999, 'pid': 3716, 'name': '\u9999\u6e2f\u7279\u522b\u884c\u653f\u533a', 'level': 2, 'region': {
          '4000': { 'id': 4000, 'pid': 3999, 'name': '\u4e2d\u897f\u533a', 'level': 3 },
          '4001': { 'id': 4001, 'pid': 3999, 'name': '\u4e1c\u533a', 'level': 3 },
          '4002': { 'id': 4002, 'pid': 3999, 'name': '\u4e5d\u9f99\u57ce\u533a', 'level': 3 },
          '4003': { 'id': 4003, 'pid': 3999, 'name': '\u89c2\u5858\u533a', 'level': 3 },
          '4004': { 'id': 4004, 'pid': 3999, 'name': '\u5357\u533a', 'level': 3 },
          '4005': { 'id': 4005, 'pid': 3999, 'name': '\u6df1\u6c34\u57d7\u533a', 'level': 3 },
          '4006': { 'id': 4006, 'pid': 3999, 'name': '\u6e7e\u4ed4\u533a', 'level': 3 },
          '4007': { 'id': 4007, 'pid': 3999, 'name': '\u9ec4\u5927\u4ed9\u533a', 'level': 3 },
          '4008': { 'id': 4008, 'pid': 3999, 'name': '\u6cb9\u5c16\u65fa\u533a', 'level': 3 },
          '4009': { 'id': 4009, 'pid': 3999, 'name': '\u79bb\u5c9b\u533a', 'level': 3 },
          '4010': { 'id': 4010, 'pid': 3999, 'name': '\u8475\u9752\u533a', 'level': 3 },
          '4011': { 'id': 4011, 'pid': 3999, 'name': '\u5317\u533a', 'level': 3 },
          '4012': { 'id': 4012, 'pid': 3999, 'name': '\u897f\u8d21\u533a', 'level': 3 },
          '4013': { 'id': 4013, 'pid': 3999, 'name': '\u6c99\u7530\u533a', 'level': 3 },
          '4014': { 'id': 4014, 'pid': 3999, 'name': '\u5c6f\u95e8\u533a', 'level': 3 },
          '4015': { 'id': 4015, 'pid': 3999, 'name': '\u5927\u57d4\u533a', 'level': 3 },
          '4016': { 'id': 4016, 'pid': 3999, 'name': '\u8343\u6e7e\u533a', 'level': 3 },
          '4017': { 'id': 4017, 'pid': 3999, 'name': '\u5143\u6717\u533a', 'level': 3 }
        }
      }
    }
  },
  '3738': {
    'id': 3738,
    'pid': 0,
    'name': '\u6fb3\u95e8\u7279\u522b\u884c\u653f\u533a',
    'level': 1,
    'city': {
      '3739': {
        'id': 3739,
        'pid': 3738,
        'name': '\u6fb3\u95e8\u534a\u5c9b',
        'level': 2,
        'region': {
          '3740': { 'id': 3740, 'pid': 3739, 'name': '\u82b1\u5730\u739b\u5802\u533a', 'level': 3 },
          '3741': { 'id': 3741, 'pid': 3739, 'name': '\u5723\u5b89\u591a\u5c3c\u5802\u533a', 'level': 3 },
          '3742': { 'id': 3742, 'pid': 3739, 'name': '\u5927\u5802\u533a', 'level': 3 },
          '3743': { 'id': 3743, 'pid': 3739, 'name': '\u671b\u5fb7\u5802\u533a', 'level': 3 },
          '3744': { 'id': 3744, 'pid': 3739, 'name': '\u98ce\u987a\u5802\u533a', 'level': 3 }
        }
      },
      '3745': {
        'id': 3745,
        'pid': 3738,
        'name': '\u6c39\u4ed4\u5c9b',
        'level': 2,
        'region': { '3746': { 'id': 3746, 'pid': 3745, 'name': '\u5609\u6a21\u5802\u533a', 'level': 3 }}
      },
      '3747': {
        'id': 3747,
        'pid': 3738,
        'name': '\u8def\u73af\u5c9b',
        'level': 2,
        'region': { '3748': { 'id': 3748, 'pid': 3747, 'name': '\u5723\u65b9\u6d4e\u5404\u5802\u533a', 'level': 3 }}
      }
    }
  }
}

export default REGIONS
