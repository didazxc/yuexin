import argparse
import numpy as np
import mrcfile
from functools import reduce
from scipy import ndimage
from PIL import Image

parser = argparse.ArgumentParser(description='convert mrc to png')
parser.add_argument('filePath', type=str, help='the file path of origin img')
args = parser.parse_args()


data=[]
with mrcfile.open(args.filePath) as mrc:
    size=len(mrc.data)
    if(size>1):
        data=reduce(lambda x,y:x+y,mrc.data)
        data/=size
    else:
        data=mrc.data[0]

data=ndimage.gaussian_filter(data,1)

dmax=np.max(data)
dmin=np.min(data)
data=(data-dmin)*255/(dmax-dmin)
im = Image.fromarray(data).convert('L')
im.save(args.filePath+".png")

