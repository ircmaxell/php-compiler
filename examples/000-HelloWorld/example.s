	.text
	.file	"main"
	.globl	__ref__init
	.p2align	4, 0x90
	.type	__ref__init,@function
__ref__init:
	.cfi_startproc
	movl	%edi, -4(%rsp)
	movq	%rsi, -16(%rsp)
	retq
.Lfunc_end0:
	.size	__ref__init, .Lfunc_end0-__ref__init
	.cfi_endproc

	.globl	__ref__addref
	.p2align	4, 0x90
	.type	__ref__addref,@function
__ref__addref:
	.cfi_startproc
	cmpq	$0, %rdi
	movq	%rdi, -8(%rsp)
	je	.LBB1_4
	movq	-8(%rsp), %rax
	movl	(%rax), %ecx
	movl	4(%rax), %edx
	andl	$1, %edx
	cmpl	$0, %edx
	movl	%ecx, -12(%rsp)
	jne	.LBB1_3
.LBB1_2:
	retq
.LBB1_3:
	jmp	.LBB1_2
.LBB1_4:
	retq
.Lfunc_end1:
	.size	__ref__addref, .Lfunc_end1-__ref__addref
	.cfi_endproc

	.globl	__ref__delref
	.p2align	4, 0x90
	.type	__ref__delref,@function
__ref__delref:
	.cfi_startproc
	subq	$24, %rsp
.Lcfi0:
	.cfi_def_cfa_offset 32
	cmpq	$0, %rdi
	movq	%rdi, 16(%rsp)
	je	.LBB2_6
	movq	16(%rsp), %rax
	movl	(%rax), %ecx
	movl	4(%rax), %edx
	andl	$1, %edx
	cmpl	$0, %edx
	movl	%ecx, 12(%rsp)
	jne	.LBB2_3
.LBB2_2:
	addq	$24, %rsp
	retq
.LBB2_3:
	movl	12(%rsp), %eax
	subl	$1, %eax
	cmpl	$0, %eax
	jle	.LBB2_5
.LBB2_4:
	jmp	.LBB2_2
.LBB2_5:
	movq	16(%rsp), %rax
	movabsq	$__mm__free, %rcx
	movq	%rax, %rdi
	callq	*%rcx
	jmp	.LBB2_4
.LBB2_6:
	addq	$24, %rsp
	retq
.Lfunc_end2:
	.size	__ref__delref, .Lfunc_end2-__ref__delref
	.cfi_endproc

	.globl	__ref__separate
	.p2align	4, 0x90
	.type	__ref__separate,@function
__ref__separate:
	.cfi_startproc
	subq	$24, %rsp
.Lcfi1:
	.cfi_def_cfa_offset 32
	movq	(%rdi), %rax
	cmpq	$0, %rax
	movq	%rdi, 16(%rsp)
	movq	%rax, 8(%rsp)
	je	.LBB3_6
	movq	8(%rsp), %rax
	movl	(%rax), %ecx
	movl	4(%rax), %edx
	movl	%edx, %esi
	andl	$1, %esi
	cmpl	$0, %esi
	movl	%ecx, 4(%rsp)
	movl	%edx, (%rsp)
	jne	.LBB3_3
.LBB3_2:
	addq	$24, %rsp
	retq
.LBB3_3:
	movl	4(%rsp), %eax
	cmpl	$1, %eax
	jg	.LBB3_5
.LBB3_4:
	jmp	.LBB3_2
.LBB3_5:
	movabsq	$__ref__separate_ex, %rax
	movq	16(%rsp), %rdi
	movl	(%rsp), %esi
	callq	*%rax
	jmp	.LBB3_4
.LBB3_6:
	addq	$24, %rsp
	retq
.Lfunc_end3:
	.size	__ref__separate, .Lfunc_end3-__ref__separate
	.cfi_endproc

	.globl	__ref__separate_ex
	.p2align	4, 0x90
	.type	__ref__separate_ex,@function
__ref__separate_ex:
	.cfi_startproc
	subq	$24, %rsp
.Lcfi2:
	.cfi_def_cfa_offset 32
	movq	(%rdi), %rax
	movabsq	$__ref__delref, %rcx
	movq	%rdi, 16(%rsp)
	movq	%rax, %rdi
	movq	%rax, 8(%rsp)
	movl	%esi, 4(%rsp)
	callq	*%rcx
	movl	4(%rsp), %esi
	andl	$1020, %esi
	cmpl	$4, %esi
	je	.LBB4_2
.LBB4_1:
	addq	$24, %rsp
	retq
.LBB4_2:
	movq	8(%rsp), %rax
	movabsq	$__string__separate, %rcx
	movq	%rax, %rdi
	callq	*%rcx
	movq	16(%rsp), %rcx
	movq	%rax, (%rcx)
	jmp	.LBB4_1
.Lfunc_end4:
	.size	__ref__separate_ex, .Lfunc_end4-__ref__separate_ex
	.cfi_endproc

	.globl	__mm__malloc
	.p2align	4, 0x90
	.type	__mm__malloc,@function
__mm__malloc:
	.cfi_startproc
	pushq	%rax
.Lcfi3:
	.cfi_def_cfa_offset 16
	movabsq	$malloc, %rax
	callq	*%rax
	popq	%rcx
	retq
.Lfunc_end5:
	.size	__mm__malloc, .Lfunc_end5-__mm__malloc
	.cfi_endproc

	.globl	__mm__realloc
	.p2align	4, 0x90
	.type	__mm__realloc,@function
__mm__realloc:
	.cfi_startproc
	pushq	%rax
.Lcfi4:
	.cfi_def_cfa_offset 16
	movabsq	$realloc, %rax
	callq	*%rax
	popq	%rcx
	retq
.Lfunc_end6:
	.size	__mm__realloc, .Lfunc_end6-__mm__realloc
	.cfi_endproc

	.globl	__mm__free
	.p2align	4, 0x90
	.type	__mm__free,@function
__mm__free:
	.cfi_startproc
	pushq	%rax
.Lcfi5:
	.cfi_def_cfa_offset 16
	movabsq	$free, %rax
	callq	*%rax
	popq	%rax
	retq
.Lfunc_end7:
	.size	__mm__free, .Lfunc_end7-__mm__free
	.cfi_endproc

	.globl	__string__strlen
	.p2align	4, 0x90
	.type	__string__strlen,@function
__string__strlen:
	.cfi_startproc
	movq	8(%rdi), %rax
	retq
.Lfunc_end8:
	.size	__string__strlen, .Lfunc_end8-__string__strlen
	.cfi_endproc

	.globl	__string__alloc
	.p2align	4, 0x90
	.type	__string__alloc,@function
__string__alloc:
	.cfi_startproc
	subq	$56, %rsp
.Lcfi6:
	.cfi_def_cfa_offset 64
	xorl	%eax, %eax
	movl	%eax, %ecx
	addq	$17, %rcx
	movq	%rdi, %rdx
	addq	$1, %rdx
	addq	%rdi, %rcx
	movabsq	$__mm__malloc, %rsi
	movq	%rdi, 48(%rsp)
	movq	%rcx, %rdi
	movq	%rdx, 40(%rsp)
	callq	*%rsi
	movl	$5, %r8d
	xorl	%r9d, %r9d
	movq	%rax, %rcx
	movq	48(%rsp), %rdx
	movq	%rdx, 8(%rax)
	movq	%rcx, %rax
	addq	$16, %rax
	xorl	%esi, %esi
	movabsq	$memset, %rdi
	movq	%rdi, 32(%rsp)
	movq	%rax, %rdi
	movq	40(%rsp), %rdx
	movq	32(%rsp), %rax
	movq	%rcx, 24(%rsp)
	movl	%r8d, 20(%rsp)
	movl	%r9d, 16(%rsp)
	callq	*%rax
	movq	24(%rsp), %rcx
	movabsq	$__ref__init, %rdx
	movl	$5, %edi
	movq	%rcx, %rsi
	movq	%rax, 8(%rsp)
	callq	*%rdx
	movq	24(%rsp), %rax
	addq	$56, %rsp
	retq
.Lfunc_end9:
	.size	__string__alloc, .Lfunc_end9-__string__alloc
	.cfi_endproc

	.globl	__string__realloc
	.p2align	4, 0x90
	.type	__string__realloc,@function
__string__realloc:
	.cfi_startproc
	subq	$72, %rsp
.Lcfi7:
	.cfi_def_cfa_offset 80
	movq	%rdi, %rax
	movabsq	$__ref__separate, %rcx
	movq	%rdi, 64(%rsp)
	movq	%rax, %rdi
	movq	%rsi, 56(%rsp)
	callq	*%rcx
	movq	64(%rsp), %rax
	movq	(%rax), %rcx
	cmpq	$0, %rcx
	movq	%rcx, 48(%rsp)
	je	.LBB10_4
	xorl	%eax, %eax
	movl	%eax, %ecx
	addq	$17, %rcx
	movq	48(%rsp), %rdx
	movq	8(%rdx), %rsi
	movq	56(%rsp), %rdi
	addq	%rdi, %rcx
	movabsq	$__mm__realloc, %r8
	movq	%rdx, %rdi
	movq	%rsi, 40(%rsp)
	movq	%rcx, %rsi
	callq	*%r8
	movq	%rax, %rcx
	movq	56(%rsp), %rdx
	movq	%rdx, 8(%rax)
	addq	$16, %rcx
	movq	40(%rsp), %rax
	cmpq	%rax, %rdx
	movq	%rcx, 32(%rsp)
	jg	.LBB10_3
.LBB10_2:
	addq	$72, %rsp
	retq
.LBB10_3:
	xorl	%eax, %eax
	movq	32(%rsp), %rcx
	movq	40(%rsp), %rdx
	addq	%rdx, %rcx
	movq	56(%rsp), %rsi
	subq	%rdx, %rsi
	xorl	%edi, %edi
	movabsq	$memset, %r8
	movl	%edi, 28(%rsp)
	movq	%rcx, %rdi
	movl	28(%rsp), %r9d
	movq	%rsi, 16(%rsp)
	movl	%r9d, %esi
	movq	16(%rsp), %rdx
	movl	%eax, 12(%rsp)
	callq	*%r8
	movq	%rax, (%rsp)
	jmp	.LBB10_2
.LBB10_4:
	movabsq	$__string__alloc, %rax
	movq	56(%rsp), %rdi
	callq	*%rax
	movq	64(%rsp), %rdi
	movq	%rax, (%rdi)
	addq	$72, %rsp
	retq
.Lfunc_end10:
	.size	__string__realloc, .Lfunc_end10-__string__realloc
	.cfi_endproc

	.globl	__string__init
	.p2align	4, 0x90
	.type	__string__init,@function
__string__init:
	.cfi_startproc
	subq	$40, %rsp
.Lcfi8:
	.cfi_def_cfa_offset 48
	movabsq	$__string__alloc, %rax
	movq	%rdi, 32(%rsp)
	movq	%rsi, 24(%rsp)
	callq	*%rax
	movq	%rax, %rsi
	addq	$16, %rsi
	movabsq	$memcpy, %rdi
	movq	%rdi, 16(%rsp)
	movq	%rsi, %rdi
	movq	24(%rsp), %rsi
	movq	32(%rsp), %rdx
	movq	16(%rsp), %rcx
	movq	%rax, 8(%rsp)
	callq	*%rcx
	movq	8(%rsp), %rcx
	movq	%rax, (%rsp)
	movq	%rcx, %rax
	addq	$40, %rsp
	retq
.Lfunc_end11:
	.size	__string__init, .Lfunc_end11-__string__init
	.cfi_endproc

	.globl	__string__separate
	.p2align	4, 0x90
	.type	__string__separate,@function
__string__separate:
	.cfi_startproc
	subq	$40, %rsp
.Lcfi9:
	.cfi_def_cfa_offset 48
	movq	8(%rdi), %rax
	movabsq	$__string__alloc, %rcx
	movq	%rdi, 32(%rsp)
	movq	%rax, %rdi
	movq	%rax, 24(%rsp)
	callq	*%rcx
	movq	32(%rsp), %rcx
	addq	$16, %rcx
	movq	%rax, %rdi
	addq	$16, %rdi
	movq	24(%rsp), %rdx
	addq	$1, %rdx
	movabsq	$memcpy, %rsi
	movq	%rsi, 16(%rsp)
	movq	%rcx, %rsi
	movq	16(%rsp), %rcx
	movq	%rax, 8(%rsp)
	callq	*%rcx
	movq	8(%rsp), %rcx
	movq	%rax, (%rsp)
	movq	%rcx, %rax
	addq	$40, %rsp
	retq
.Lfunc_end12:
	.size	__string__separate, .Lfunc_end12-__string__separate
	.cfi_endproc

	.globl	__value__valueDelref
	.p2align	4, 0x90
	.type	__value__valueDelref,@function
__value__valueDelref:
	.cfi_startproc
	pushq	%rax
.Lcfi10:
	.cfi_def_cfa_offset 16
	movb	(%rdi), %al
	andb	$-128, %al
	cmpb	$0, %al
	movq	%rdi, (%rsp)
	jne	.LBB13_2
.LBB13_1:
	popq	%rax
	retq
.LBB13_2:
	movq	(%rsp), %rax
	addq	$1, %rax
	movabsq	$__ref__delref, %rcx
	movq	%rax, %rdi
	callq	*%rcx
	jmp	.LBB13_1
.Lfunc_end13:
	.size	__value__valueDelref, .Lfunc_end13-__value__valueDelref
	.cfi_endproc

	.globl	__value__toNumeric
	.p2align	4, 0x90
	.type	__value__toNumeric,@function
__value__toNumeric:
	.cfi_startproc
	pushq	%rbp
.Lcfi11:
	.cfi_def_cfa_offset 16
.Lcfi12:
	.cfi_offset %rbp, -16
	movq	%rsp, %rbp
.Lcfi13:
	.cfi_def_cfa_register %rbp
	subq	$80, %rsp
	movb	(%rdi), %al
	movb	%al, %cl
	subb	$-122, %cl
	movq	%rdi, -8(%rbp)
	movb	%al, -9(%rbp)
	movb	%cl, -10(%rbp)
	je	.LBB14_4
	jmp	.LBB14_8
.LBB14_8:
	movb	-9(%rbp), %al
	subb	$1, %al
	movb	%al, -11(%rbp)
	je	.LBB14_2
	jmp	.LBB14_9
.LBB14_9:
	movb	-9(%rbp), %al
	subb	$3, %al
	movb	%al, -12(%rbp)
	je	.LBB14_3
	jmp	.LBB14_7
.LBB14_1:
	xorl	%eax, %eax
	movl	%eax, %ecx
	movq	%rsp, %rdx
	addq	$-16, %rdx
	movq	%rdx, %rsp
	movabsq	$__value__writeLong, %rsi
	xorl	%eax, %eax
	movl	%eax, %edi
	movq	%rdi, -24(%rbp)
	movq	%rdx, %rdi
	movq	-24(%rbp), %r8
	movq	%rsi, -32(%rbp)
	movq	%r8, %rsi
	movq	-32(%rbp), %r9
	movq	%rcx, -40(%rbp)
	movq	%rdx, -48(%rbp)
	callq	*%r9
	movq	-48(%rbp), %rax
	movq	%rbp, %rsp
	popq	%rbp
	retq
.LBB14_2:
	movq	-8(%rbp), %rax
	movabsq	$__ref__addref, %rcx
	movq	%rax, %rdi
	callq	*%rcx
	movq	-8(%rbp), %rax
	movq	%rbp, %rsp
	popq	%rbp
	retq
.LBB14_3:
	movq	-8(%rbp), %rax
	movabsq	$__ref__addref, %rcx
	movq	%rax, %rdi
	callq	*%rcx
	movq	-8(%rbp), %rax
	movq	%rbp, %rsp
	popq	%rbp
	retq
.LBB14_4:
	movq	-8(%rbp), %rax
	addq	$1, %rax
	movq	%rax, %rcx
	addq	$8, %rcx
	movabsq	$__value__toNumeric, %rdx
	movq	%rcx, %rdi
	movq	%rax, -56(%rbp)
	movq	%rcx, -64(%rbp)
	callq	*%rdx
	movq	-64(%rbp), %rcx
	cmpq	%rcx, %rax
	movq	%rax, -72(%rbp)
	je	.LBB14_6
.LBB14_5:
	movq	-72(%rbp), %rax
	movq	%rbp, %rsp
	popq	%rbp
	retq
.LBB14_6:
	movq	-56(%rbp), %rax
	movabsq	$__ref__addref, %rcx
	movq	%rax, %rdi
	callq	*%rcx
	jmp	.LBB14_5
.LBB14_7:
	jmp	.LBB14_1
.Lfunc_end14:
	.size	__value__toNumeric, .Lfunc_end14-__value__toNumeric
	.cfi_endproc

	.globl	__value__readLong
	.p2align	4, 0x90
	.type	__value__readLong,@function
__value__readLong:
	.cfi_startproc
	subq	$24, %rsp
.Lcfi14:
	.cfi_def_cfa_offset 32
	movb	(%rdi), %al
	movb	%al, %cl
	subb	$-122, %cl
	movq	%rdi, 16(%rsp)
	movb	%al, 15(%rsp)
	movb	%cl, 14(%rsp)
	je	.LBB15_4
	jmp	.LBB15_6
.LBB15_6:
	movb	15(%rsp), %al
	subb	$1, %al
	movb	%al, 13(%rsp)
	je	.LBB15_2
	jmp	.LBB15_7
.LBB15_7:
	movb	15(%rsp), %al
	subb	$3, %al
	movb	%al, 12(%rsp)
	je	.LBB15_3
	jmp	.LBB15_5
.LBB15_1:
	xorl	%eax, %eax
	addq	$24, %rsp
	retq
.LBB15_2:
	movq	16(%rsp), %rax
	movq	1(%rax), %rax
	addq	$24, %rsp
	retq
.LBB15_3:
	movq	16(%rsp), %rax
	cvttsd2si	1(%rax), %rax
	addq	$24, %rsp
	retq
.LBB15_4:
	movq	16(%rsp), %rax
	addq	$1, %rax
	addq	$8, %rax
	movabsq	$__value__readLong, %rcx
	movq	%rax, %rdi
	callq	*%rcx
	addq	$24, %rsp
	retq
.LBB15_5:
	jmp	.LBB15_1
.Lfunc_end15:
	.size	__value__readLong, .Lfunc_end15-__value__readLong
	.cfi_endproc

	.globl	__value__writeLong
	.p2align	4, 0x90
	.type	__value__writeLong,@function
__value__writeLong:
	.cfi_startproc
	subq	$24, %rsp
.Lcfi15:
	.cfi_def_cfa_offset 32
	movabsq	$__value__valueDelref, %rax
	movq	%rdi, 16(%rsp)
	movq	%rsi, 8(%rsp)
	callq	*%rax
	movq	16(%rsp), %rax
	movb	$1, (%rax)
	movq	8(%rsp), %rsi
	movq	%rsi, 1(%rax)
	addq	$24, %rsp
	retq
.Lfunc_end16:
	.size	__value__writeLong, .Lfunc_end16-__value__writeLong
	.cfi_endproc

	.globl	__value__readDouble
	.p2align	4, 0x90
	.type	__value__readDouble,@function
__value__readDouble:
	.cfi_startproc
	subq	$24, %rsp
.Lcfi16:
	.cfi_def_cfa_offset 32
	movb	(%rdi), %al
	movb	%al, %cl
	subb	$-122, %cl
	movq	%rdi, 16(%rsp)
	movb	%al, 15(%rsp)
	movb	%cl, 14(%rsp)
	je	.LBB17_4
	jmp	.LBB17_6
.LBB17_6:
	movb	15(%rsp), %al
	subb	$1, %al
	movb	%al, 13(%rsp)
	je	.LBB17_2
	jmp	.LBB17_7
.LBB17_7:
	movb	15(%rsp), %al
	subb	$3, %al
	movb	%al, 12(%rsp)
	je	.LBB17_3
	jmp	.LBB17_5
.LBB17_1:
	xorps	%xmm0, %xmm0
	addq	$24, %rsp
	retq
.LBB17_2:
	movq	16(%rsp), %rax
	cvtsi2sdq	1(%rax), %xmm0
	addq	$24, %rsp
	retq
.LBB17_3:
	movq	16(%rsp), %rax
	movsd	1(%rax), %xmm0
	addq	$24, %rsp
	retq
.LBB17_4:
	movq	16(%rsp), %rax
	addq	$1, %rax
	addq	$8, %rax
	movabsq	$__value__readDouble, %rcx
	movq	%rax, %rdi
	callq	*%rcx
	addq	$24, %rsp
	retq
.LBB17_5:
	jmp	.LBB17_1
.Lfunc_end17:
	.size	__value__readDouble, .Lfunc_end17-__value__readDouble
	.cfi_endproc

	.globl	__value__writeDouble
	.p2align	4, 0x90
	.type	__value__writeDouble,@function
__value__writeDouble:
	.cfi_startproc
	subq	$24, %rsp
.Lcfi17:
	.cfi_def_cfa_offset 32
	movabsq	$__value__valueDelref, %rax
	movq	%rdi, 16(%rsp)
	movsd	%xmm0, 8(%rsp)
	callq	*%rax
	movq	16(%rsp), %rax
	movb	$3, (%rax)
	movsd	8(%rsp), %xmm0
	movsd	%xmm0, 1(%rax)
	addq	$24, %rsp
	retq
.Lfunc_end18:
	.size	__value__writeDouble, .Lfunc_end18-__value__writeDouble
	.cfi_endproc

	.globl	__init__
	.p2align	4, 0x90
	.type	__init__,@function
__init__:
	.cfi_startproc
	pushq	%rax
.Lcfi18:
	.cfi_def_cfa_offset 16
	movabsq	$__unnamed_1, %rsi
	movabsq	$__string__init, %rax
	xorl	%ecx, %ecx
	movl	%ecx, %edi
	movq	%rax, (%rsp)
	callq	*%rax
	movabsq	$string_const_0, %rsi
	movq	%rax, (%rsi)
	movabsq	$"Hello World\n", %rsi
	movl	$12, %ecx
	movl	%ecx, %edi
	movq	(%rsp), %rax
	callq	*%rax
	movabsq	$string_const_1, %rsi
	movq	%rax, (%rsi)
	popq	%rax
	retq
.Lfunc_end19:
	.size	__init__, .Lfunc_end19-__init__
	.cfi_endproc

	.globl	__shutdown__
	.p2align	4, 0x90
	.type	__shutdown__,@function
__shutdown__:
	.cfi_startproc
	pushq	%rax
.Lcfi19:
	.cfi_def_cfa_offset 16
	movabsq	$string_const_0, %rax
	movq	(%rax), %rdi
	movabsq	$__mm__free, %rax
	callq	*%rax
	movabsq	$string_const_1, %rax
	movq	(%rax), %rax
	movabsq	$__mm__free, %rdi
	movq	%rdi, (%rsp)
	movq	%rax, %rdi
	movq	(%rsp), %rax
	callq	*%rax
	popq	%rax
	retq
.Lfunc_end20:
	.size	__shutdown__, .Lfunc_end20-__shutdown__
	.cfi_endproc

	.globl	str_repeat
	.p2align	4, 0x90
	.type	str_repeat,@function
str_repeat:
	.cfi_startproc
	subq	$216, %rsp
.Lcfi20:
	.cfi_def_cfa_offset 224
	leaq	208(%rsp), %rax
	movq	$0, 208(%rsp)
	movq	$0, 176(%rsp)
	movq	$0, 168(%rsp)
	movabsq	$__ref__delref, %rcx
	leaq	176(%rsp), %rdx
	movq	%rdi, 152(%rsp)
	movq	%rdx, %rdi
	movq	%rdx, 144(%rsp)
	movq	%rsi, 136(%rsp)
	movq	%rax, 128(%rsp)
	callq	*%rcx
	movq	152(%rsp), %rax
	movq	%rax, 176(%rsp)
	movabsq	$__ref__addref, %rcx
	movq	144(%rsp), %rdi
	callq	*%rcx
	movq	136(%rsp), %rax
	movq	%rax, 192(%rsp)
	movabsq	$string_const_0, %rcx
	movq	(%rcx), %rcx
	movq	128(%rsp), %rdx
	movabsq	$__ref__delref, %rsi
	movq	%rdx, %rdi
	movq	%rcx, 120(%rsp)
	callq	*%rsi
	leaq	208(%rsp), %rax
	movq	120(%rsp), %rcx
	movq	%rcx, 208(%rsp)
	movabsq	$__ref__addref, %rcx
	movq	%rax, %rdi
	callq	*%rcx
	movq	$0, 200(%rsp)
.LBB21_1:
	movq	200(%rsp), %rax
	cmpq	192(%rsp), %rax
	setl	%cl
	andb	$1, %cl
	movb	%cl, 191(%rsp)
	testb	$1, 191(%rsp)
	jne	.LBB21_2
	jmp	.LBB21_4
.LBB21_2:
	leaq	208(%rsp), %rax
	movq	208(%rsp), %rcx
	movq	176(%rsp), %rdx
	movq	8(%rcx), %rsi
	movq	8(%rdx), %rdi
	movq	%rsi, %r8
	addq	%rdi, %r8
	movabsq	$__string__realloc, %r9
	leaq	208(%rsp), %r10
	movq	%rdi, 112(%rsp)
	movq	%r10, %rdi
	movq	%rsi, 104(%rsp)
	movq	%r8, %rsi
	movq	%rax, 96(%rsp)
	movq	%rcx, 88(%rsp)
	movq	%rdx, 80(%rsp)
	movq	%r8, 72(%rsp)
	callq	*%r9
	movq	208(%rsp), %rax
	movq	72(%rsp), %rcx
	movq	%rcx, 8(%rax)
	movq	%rax, %rdx
	addq	$16, %rdx
	movq	88(%rsp), %rsi
	addq	$16, %rsi
	movabsq	$memcpy, %rdi
	movq	%rdi, 64(%rsp)
	movq	%rdx, %rdi
	movq	104(%rsp), %r8
	movq	%rdx, 56(%rsp)
	movq	%r8, %rdx
	movq	64(%rsp), %r9
	movq	%rax, 48(%rsp)
	callq	*%r9
	movq	56(%rsp), %rcx
	movq	104(%rsp), %rdx
	addq	%rdx, %rcx
	movq	80(%rsp), %rsi
	addq	$16, %rsi
	movabsq	$memcpy, %rdi
	movq	%rdi, 40(%rsp)
	movq	%rcx, %rdi
	movq	112(%rsp), %rdx
	movq	40(%rsp), %rcx
	movq	%rax, 32(%rsp)
	callq	*%rcx
	movq	48(%rsp), %rcx
	movq	%rcx, 208(%rsp)
	movq	%rax, 24(%rsp)
	movq	200(%rsp), %rax
	addq	$1, %rax
	movq	%rax, 160(%rsp)
	movq	160(%rsp), %rax
	movq	%rax, 200(%rsp)
	jmp	.LBB21_1
.LBB21_4:
	leaq	208(%rsp), %rax
	movabsq	$__ref__addref, %rcx
	movq	%rax, %rdi
	callq	*%rcx
	leaq	208(%rsp), %rax
	movq	208(%rsp), %rcx
	movabsq	$__ref__delref, %rdi
	movq	%rdi, 16(%rsp)
	movq	%rax, %rdi
	movq	16(%rsp), %rax
	movq	%rcx, 8(%rsp)
	callq	*%rax
	leaq	176(%rsp), %rax
	movabsq	$__ref__delref, %rcx
	movq	%rax, %rdi
	callq	*%rcx
	leaq	168(%rsp), %rax
	movabsq	$__ref__delref, %rcx
	movq	%rax, %rdi
	callq	*%rcx
	movq	8(%rsp), %rax
	addq	$216, %rsp
	retq
.Lfunc_end21:
	.size	str_repeat, .Lfunc_end21-str_repeat
	.cfi_endproc

	.globl	internal_1
	.p2align	4, 0x90
	.type	internal_1,@function
internal_1:
	.cfi_startproc
	pushq	%rax
.Lcfi21:
	.cfi_def_cfa_offset 16
	movabsq	$string_const_1, %rax
	movq	(%rax), %rax
	movq	8(%rax), %rsi
	addq	$16, %rax
	movabsq	$"%.*s", %rdi
	movabsq	$printf, %rcx
	xorl	%edx, %edx
	movb	%dl, %r8b
	movq	%rax, %rdx
	movb	%r8b, %al
	callq	*%rcx
	movl	%eax, 4(%rsp)
	popq	%rax
	retq
.Lfunc_end22:
	.size	internal_1, .Lfunc_end22-internal_1
	.cfi_endproc

	.type	string_const_0,@object
	.bss
	.globl	string_const_0
	.p2align	3
string_const_0:
	.quad	0
	.size	string_const_0, 8

	.type	__unnamed_1,@object
	.globl	__unnamed_1
__unnamed_1:
	.zero	1
	.size	__unnamed_1, 1

	.type	string_const_1,@object
	.globl	string_const_1
	.p2align	3
string_const_1:
	.quad	0
	.size	string_const_1, 8

	.type	"Hello World\n",@object
	.data
	.globl	"Hello World\n"
"Hello World\n":
	.asciz	"Hello World\n"
	.size	"Hello World\n", 13

	.type	"%.*s",@object
	.globl	"%.*s"
"%.*s":
	.asciz	"%.*s"
	.size	"%.*s", 5


	.section	".note.GNU-stack","",@progbits
